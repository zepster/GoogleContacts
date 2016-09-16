<?php

namespace GoogleContacts;

class Client
{
	private $auth;

	public function getAuth() {
		if (!isset($this->auth)) {
			$this->auth = new OAuth;
		}
		return $this->auth;
	}

	public function setCredentials(Credentials $credentials)
	{
		$this->getAuth()->setCredentials($credentials);
		return $this;
	}

	public function addContact(ContactData $contact_data)
	{
		// @todo реализация

		$request = new Request('http://localhost');
		return $this->getAuth()->authenticatedRequest($request);
	}

}