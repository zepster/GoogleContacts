<?php

namespace GoogleContacts;

class Oauth
{
	const URL = 'https://www.googleapis.com/oauth2/v4/token';
	CONST GRANT_TYPE = 'urn:ietf:params:oauth:grant-type:jwt-bearer';

	private $token; // @todo Token instance

	public function __construct()
	{
		$this->token = $this->requestToken();
	}

	public function getToken()
	{
		if ($this->token->isExpired()) {
			$this->token = $this->requestToken();
		}
		return $this->token;
	}

	private function requestToken()
	{
		$request = new Request(self::URL, ["Content-type: application/x-www-form-urlencoded"]);

		$request_body = http_build_query([
			'grant_type' => self::GRANT_TYPE,
			'assertion' => '' // @todo assertion
		]);

		$response = $request->setBody($request_body)->send();

		$status_code = $response->getStatusCode();
		$response_data = $response->getBody();

		if (200 == $status_code) {
			return json_decode($response['response'], true); // @todo return Token instance
		} else {
			throw new \Exception('Oauth failed with status: ' . $status_code . ', response: ' . $response_data);
		}
	}

}
