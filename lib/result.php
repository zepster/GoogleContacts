<?php

namespace GoogleContacts;

class Result
{
	const STATUS_SUCCESS = 0;
	const STATUS_ERROR = 1;

	const ERROR_SOME_ERROR = 1;

	private $status;
	private $error_code;
	private $error_message;

	public function __construct($status, $error_code = null, $error_message = '')
	{
		$this->status = $status;
		$this->error_code = $error_code;
		$this->error_message = $error_message;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getErrorCode()
	{
		return $this->error_code;
	}

	public function getErrorMessage()
	{
		return $this->error_message;
	}

}