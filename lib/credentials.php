<?php

namespace GoogleContacts;

class Credentials
{
	public $private_key;
	public $service_account_email;
	public $target_user_email;

	public function __construct($service_account_json_key, $target_user_email = null)
	{
		$service_account_config = json_decode($service_account_json_key, true);

		if (null == $service_account_config) {
			throw new \Exception('Could not decode service account json key');
		}

		if (!isset($service_account_config['private_key'], $service_account_config['client_email'])) {
			throw new \Exception('Invalid service account json key format');
		}

		$this->private_key = $service_account_config['private_key'];
		$this->service_account_email = $service_account_config['client_email'];
		$this->target_user_email = $target_user_email;
	}

}