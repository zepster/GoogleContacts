<?php

namespace GoogleContacts;

use Firebase\JWT\JWT;

class OAuth
{
	const URL = 'https://www.googleapis.com/oauth2/v4/token';
	const GRANT_TYPE = 'urn:ietf:params:oauth:grant-type:jwt-bearer';
	const MAX_TOKEN_LIFETIME_SECS = 3600;

	/** @var Credentials */
	private $credentials;
	private $token = array();

	public function setCredentials(Credentials $credentials)
	{
		$this->credentials = $credentials;
	}

	public function authenticatedRequest(Request $request)
	{
		return $this->sign($request)->execute();
	}

	public function getToken()
	{
		if ($this->isTokenExpired()) {
			$this->requestToken();
		}
		return $this->token;
	}

	private function sign(Request $request)
	{
		if (empty($this->token) && null == $this->credentials) {
			return $request;
		}

		if ($this->isTokenExpired()) {
			$this->requestToken();
		}

		$request->setHeaders(['Authorization: Bearer ' . $this->token['access_token']]);
		return $request;
	}

	private function isTokenExpired()
	{
		if (empty($this->token) || !isset($this->token['created'])) {
			return true;
		}

		$expired = ($this->token['created'] + $this->token['expires_in'] - 30) < time();

		return $expired;
	}

	private function requestToken()
	{
		$request = new Request(self::URL, ["Content-type: application/x-www-form-urlencoded"]);

		$request_body = http_build_query([
			'grant_type' => self::GRANT_TYPE,
			'assertion' => $this->createAssertion()
		]);

		$response = $request->setBody($request_body)->execute();

		$status_code = $response->getStatusCode();
		$response_body = $response->getBody();

		if (200 == $status_code) {
			$token_data = json_decode($response_body, true);

			if (null == $token_data) {
				throw new \Exception ('Could not json decode the token');
			}

			if (!isset($token_data['access_token'], $token_data['expires_in'])) {
				throw new \Exception ('Invalid token format');
			}

			$this->token = $token_data;
			$this->token['created'] = time();
		} else {
			throw new \Exception('Oauth failed with status: ' . $status_code . ', response: ' . $response_body);
		}
	}

	private function createAssertion()
	{
		$now = time();

		$jwt_params = array(
			'aud' => 'https://www.googleapis.com/oauth2/v4/token',
			'scope' => 'https://www.google.com/m8/feeds/',
			'iat' => $now,
			'exp' => $now + self::MAX_TOKEN_LIFETIME_SECS,
			'iss' => $this->credentials->service_account_email,
		);

		if (null !== $this->credentials->target_user_email) {
			$jwt_params['sub'] = $this->credentials->target_user_email;
		}

		return JWT::encode($jwt_params, $this->credentials->private_key, 'RS256');
	}

}
