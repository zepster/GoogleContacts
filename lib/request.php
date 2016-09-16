<?php

namespace GoogleContacts;

class Request
{
	private $handler;

	public function __construct($url, array $headers = array())
	{
		$this->handler = curl_init($url);

		curl_setopt($this->handler, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->handler, CURLOPT_SSL_VERIFYPEER, false);
	}

	public function setBody($data)
	{
		curl_setopt($this->handler, CURLOPT_POST, true);
		curl_setopt($this->handler, CURLOPT_POSTFIELDS, $data);

		return $this;
	}

	public function send()
	{
		$response_data = curl_exec($this->handler);
		$status_code = curl_getinfo($this->handler, CURLINFO_HTTP_CODE);

		if (!$response_data) {
			throw new \Exception('Request failed: ' . curl_error($this->handler));
		}

		curl_close($this->handler);

		return new Response($status_code, $response_data);
	}

}
