<?php

namespace GoogleContacts;

class Service
{
	public function __construct($service_account_json_key)
	{
		// @todo реализация
		// json_decode($service_account_json_key, true);
	}

	public function addUserContact($target_user_email, Contact $contact_data)
	{
		// @todo реализация
		return new Result(Result::STATUS_SUCCESS);
	}

}