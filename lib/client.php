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
            try {
                $groupId = $this->getGroupIdByName($contact_data->get_contacts_group_title());
                if (!$this->getContactByGroupId($contact_data, $groupId)) {
                    return $this->createContact($contact_data, $groupId);
                } else {
                    return new Response(304, 'Not Modified');
                }
            } catch (\Exception $ex)  {
                return new Response($ex->getCode(), $ex->getMessage());
            }
                
	}
        
        private function createContact(ContactData $contact_data, $groupId) 
        {
            $contactEntry = new ContactEntry(
                    $contact_data->get_first_name(),
                    $contact_data->get_last_name(),
                    $contact_data->get_email(),
                    $contact_data->get_phone_number(),
                    $groupId
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
                return new Response($status_code, $response_body);
            } else {
                throw new \Exception($response_body, $status_code);
            }           
            
        }
        
        private function getContactByGroupId(ContactData $contact_data, $groupId) 
        {
            $q = '"'. sprintf('%s %s', $contact_data->get_first_name(), $contact_data->get_last_name()).'" '.$contact_data->get_phone_number().' '.$contact_data->get_email();
            $url = 'https://www.google.com/m8/feeds/contacts/default/full?alt=json&q='.urlencode($q);
            $request = new Request(
                $url,
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
                            if ($group['deleted'] === "false" && $group['href'] == $groupId) { 
                                    return true;
                                }
                            }
                        }
                    }
                }
                return false;
            } else {
                throw new \Exception($response_body, $status_code);
            }
            
        }
        
        private function getGroupIdByName($group_title) 
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
        
        private function createGroup($group_title)
        {
            $group_entry = new GroupEntry($group_title);
            $request = new Request(
                'https://www.google.com/m8/feeds/groups/default/full?alt=json',
                ['GData-Version: 3.0', 'Content-Type: application/atom+xml']
            );
            $request->setBody($group_entry->getXml());
            $response = $this->getAuth()->authenticatedRequest($request);
            
            $status_code = $response->getStatusCode();
            $response_body = $response->getBody();
            
            if (201 == $status_code) {
                $data = json_decode($response_body, true);
                return $data['entry']['id']['$t'];
            } else {
                throw new \Exception($response_body, $status_code);
            }
        }

}