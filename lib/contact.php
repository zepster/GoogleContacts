<?php

namespace GoogleContacts;

class Contact
{
	private $first_name;
	private $last_name;
	private $email;
	private $phone_number;
	private $contacts_group_title;

	public function __construct($first_name, $last_name, $email, $phone_number, $contacts_group_title)
	{
		$this->first_name = $first_name;
		$this->last_name = $last_name;
		$this->email = $email;
		$this->phone_number = $phone_number;
		$this->contacts_group_title = $contacts_group_title;
	}

	public function get_first_name()
	{
		return $this->first_name;
	}

	public function get_last_name()
	{
		return $this->last_name;
	}

	public function get_email()
	{
		return $this->email;
	}

	public function get_phone_number()
	{
		return $this->phone_number;
	}

	public function get_contacts_group_title()
	{
		return $this->contacts_group_title;
	}

}