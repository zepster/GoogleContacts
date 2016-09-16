<?php

namespace GoogleContacts;

class Request
{
	private $url;
	private $headers = array();
	private $body;

	public function __construct($url, array $headers = [])
	{
		$this->url = $url;
		$this->setHeaders($headers);
	}

	public function setHeaders(array $headers)
	{
		if (!empty($this->headers)) {
			$headers = array_merge($this->headers, $headers);
		}

		$this->headers = $headers;

		return $this;
	}

	public function setBody($data)
	{
		$this->body = $data;

		return $this;
	}

	public function execute()
	{
		$curl = curl_init();

		if (!empty($this->headers)) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		}

		curl_setopt($curl, CURLOPT_URL, $this->url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);

		if ($this->body) {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $this->body);
		}

		$response_data = curl_exec($curl);
		$status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		if (!$response_data) {
			throw new \Exception('Request failed: ' . curl_error($curl));
		}

		curl_close($curl);

		return new Response($status_code, $response_data);
	}

}
