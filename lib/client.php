<?php

namespace GoogleContacts;

class Client
{
	private $auth;

	public function getAuth()
	{
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
		try {
			$group_title = $contact_data->getGroupTitle();

			$group_id = $this->findGroupIdByTitle($group_title);
			if (!$group_id) {
				$group_id = $this->createGroup($group_title);
			}

			$existing_contact = $this->findContactByAttributes($contact_data, $group_id);
			if ($existing_contact) {
				return new Result(Result::STATUS_EXISTS);
			} elseif ($this->createContact($contact_data, $group_id)) {
				return new Result(Result::STATUS_ADDED);
			}

		} catch (\Exception $ex) {
			return new Result(Result::STATUS_ERROR, $ex->getMessage());
		}
	}

	private function findGroupIdByTitle($group_title)
	{
		$group_id = false;
		$request = new Request(
			'https://www.google.com/m8/feeds/groups/default/full?alt=json&max-results=999999',
			['GData-Version: 3.0']
		);
		$response = $this->getAuth()->authenticatedRequest($request);

		$status_code = $response->getStatusCode();
		$response_body = $response->getBody();

		if (200 == $status_code) {
			$data = json_decode($response_body, true);
			foreach ($data['feed']['entry'] as $group) {
				if (strtolower($group['title']['$t']) === strtolower($group_title)) {
					$group_id = $group['id']['$t'];
				}
			}
		} else {
			throw new \Exception($response_body, $status_code);
		}

		if (false === $group_id) {
			$group_id = $this->createGroup($group_title);
		}
		return $group_id;

	}

	private function findContactByAttributes(ContactData $contact_data, $group_id)
	{
		$q = implode(' ', [
			$contact_data->getFirstName(),
			$contact_data->getLastName(),
			$contact_data->getPhoneNumber(),
			$contact_data->getEmail(),
		]);

		$request = new Request(
			'https://www.google.com/m8/feeds/contacts/default/full?alt=json&q=' . urlencode($q),
			['GData-Version: 3.0']
		);

		$response = $this->getAuth()->authenticatedRequest($request);

		$status_code = $response->getStatusCode();
		$response_body = $response->getBody();

		if (200 == $status_code) {
			$data = json_decode($response_body, true);
			if ($data['feed']['openSearch$totalResults']['$t'] != 0) {
				foreach ($data['feed']['entry'] as $contact) {
					if (isset($contact['gContact$groupMembershipInfo'])) {
						foreach ($contact['gContact$groupMembershipInfo'] as $group) {
							if ($group['deleted'] === 'false' && $group['href'] == $group_id) {
								return $contact;
							}
						}
					}
				}
			}
			return null;
		} else {
			throw new \Exception($response_body, $status_code);
		}
	}

	private function createGroup($group_title)
	{
		$request = new Request(
			'https://www.google.com/m8/feeds/groups/default/full?alt=json',
			['GData-Version: 3.0', 'Content-Type: application/atom+xml']
		);

		$group_entry = new GroupEntry($group_title);
		$request->setBody($group_entry->getXml());

		$response = $this->getAuth()->authenticatedRequest($request);

		$status_code = $response->getStatusCode();
		$response_body = $response->getBody();

		if (201 == $status_code) {
			$data = json_decode($response_body, true);
			if (null == $data) {
				throw new \Exception('Could not decode group response');
			}
			if (!isset($data['entry']['id']['$t'])) {
				throw new \Exception('Invalid group response format');
			}
			return $data['entry']['id']['$t'];
		} else {
			throw new \Exception($response_body, $status_code);
		}
	}

	private function createContact(ContactData $contact_data, $group_id)
	{
		$contactEntry = new ContactEntry(
			$contact_data->getFirstName(),
			$contact_data->getLastName(),
			$contact_data->getEmail(),
			$contact_data->getPhoneNumber(),
			$group_id
		);
		$request = new Request(
			'https://www.google.com/m8/feeds/contacts/default/full?alt=json',
			['GData-Version: 3.0', 'Content-Type: application/atom+xml']
		);

		$request->setBody($contactEntry->getXml());
		$response = $this->getAuth()->authenticatedRequest($request);

		$status_code = $response->getStatusCode();
		$response_body = $response->getBody();

		if (201 == $status_code) {
			return $response_body;
		} else {
			throw new \Exception($response_body, $status_code);
		}

	}

}