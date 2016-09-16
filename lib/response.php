<?php

namespace GoogleContacts;

class Response
{
	private $status_code = 200;
	private $body;

	public function __construct($status_code, $body)
	{
		$this->status_code = $status_code;
		$this->body = $body;
	}

	public function getStatusCode()
	{
		return $this->status_code;
	}

	public function getBody()
	{
		return $this->body;
	}

}