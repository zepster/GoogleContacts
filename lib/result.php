<?php

namespace GoogleContacts;

class Result
{
	const STATUS_ERROR = -1;
	const STATUS_ADDED = 0;
	const STATUS_EXISTS = 1;

	private $status_code;
	private $status_message;

	public function __construct($status, $error_message = '')
	{
		$this->status_code = $status;
		$this->status_message = $error_message;
	}

	public function getStatusCode()
	{
		return $this->status_code;
	}

	public function getStatusMessage()
	{
		return $this->status_message;
	}

}