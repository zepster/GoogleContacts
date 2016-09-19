<?php

namespace GoogleContacts;

class ContactData
{
	private $first_name;
	private $last_name;
	private $email;
	private $phone_number;
	private $group_title;

	public function __construct($first_name, $last_name, $email, $phone_number, $group_title)
	{
		$this->first_name = $first_name;
		$this->last_name = $last_name;
		$this->email = $email;
		$this->phone_number = $phone_number;
		$this->group_title = $group_title;
	}

	public function getFirstName()
	{
		return $this->first_name;
	}

	public function getLastName()
	{
		return $this->last_name;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getPhoneNumber()
	{
		return $this->phone_number;
	}

	public function getGroupTitle()
	{
		return $this->group_title;
	}

}