<?php
namespace GoogleContacts\Service;
require_once './jwt/JWT.php';

use \Firebase\JWT\JWT;
use GoogleContacts\Request\Request;
use GoogleContacts\Oauth\Oauth;

class Service {
    
    private $_config = [];
    private $_payload = [];
    private $_oauth = null;
    private $_targetUser = null;
    private $_contact = [];
    
    //тестовая группа
//    private $_tGroupId = 'http://www.google.com/m8/feeds/groups/aleha%40coderip.ru/base/1fbd2e78f9631d3';
    private $_tGroupId = null;

    /**
     * Принимаем php массив json файла из console google
     * @param array $config
     * @throws Exception
     */
    public function __construct($config = null, $user, $json) {
        if (is_null($config))
            throw new Exception ('Config not found!');
        $this->_config = $config;
        $this->_targetUser = $user;
        $this->_oauth = new Oauth();
        $this->_contact = $json;
        $this->_preparePayload();
    }
    
    // блок для токена. вынести
    private function _preparePayload() {
        $this->_payload = [
            'iss' => $this->_config['client_email'], // емайл сервис аккаунта
            'sub' => $this->_targetUser,
            'scope' => 'https://www.google.com/m8/feeds/',
            'aud' => 'https://www.googleapis.com/oauth2/v4/token',
            'exp' => time() + 60*60,
            'iat' => time()
        ];
    }
    
    public function getToken() {
        $assertion = JWT::encode($this->_payload, $this->_config['private_key'], 'RS256');
        $this->_oauth->requestToken($assertion);
    }
    
    public function testContacts(){
        $opt = [
            'url' => 'https://www.google.com/m8/feeds/groups/default/full',
            'method' => 'GET',
            'headers' => [
                $this->_oauth->getTokenHeader(),
                'GData-Version: 3.0',
            ],
        ];
        
        $response = Request::req($opt);
        var_dump($response);
    }
    
    public function groupExists() {
        var_dump("groupExists\n");
        $q = '"'.$this->_contact['group'].'"';
        $opt = [
            'url' => 'https://www.google.com/m8/feeds/groups/default/full?alt=json&q='.  urlencode($q),
            'method' => 'GET',
            'headers' => [
                $this->_oauth->getTokenHeader(),
                'GData-Version: 3.0',
            ]
        ];
        
        $response = Request::req($opt);
        if ($response['status'] !== 200) {
            throw new \Exception('Function getGroupId return fail http status');
        }
        $data = json_decode($response['response'], true);
        if ($data['feed']['openSearch$totalResults']['$t'] >= 1) {
            $this->_tGroupId = $data['feed']['entry'][0]['id']['$t'];
            var_dump("Exists\n");
            return true;
        }
        var_dump("NoExists\n");
        return false;
    }
    
    public function contactExist() {
        var_dump("contactExist\n");
        $q = '"'. sprintf('%s %s %s', $this->_contact['surname'], $this->_contact['firstname'], $this->_contact['middlename'])
            .'" '.$this->_contact['phone'].' '.$this->_contact['email'];
        $opt = [
            'url' => 'https://www.google.com/m8/feeds/contacts/default/full?alt=json&q='.urlencode($q),
            'method' => 'GET',
            'headers' => [
                $this->_oauth->getTokenHeader(),
                'GData-Version: 3.0',
            ]
        ];
        $response = Request::req($opt);
        if ($response['status'] !== 200) {
            throw new \Exception('Function getGroupId return fail http status ('.$response['status'].') '. $response['response']);
        }
        $data = json_decode($response['response'], true);
        // точное совпадение/ проверить группу
        if ($data['feed']['openSearch$totalResults']['$t'] == 1) {
            if (count($data['feed']['entry'][0]['gContact$groupMembershipInfo']) == 1) {
                $this->_tGroupId = $data['feed']['entry'][0]['gContact$groupMembershipInfo'][0]['href'];
                var_dump("contactExist\n");
                return true;
            }
        }
        var_dump("NOTExist\n");
        return false;
    }
    
    public function createContact() {
        var_dump("createContact\n");
        $xml = $this->_createUserXml();
        $opt = [
            'url' => 'https://www.google.com/m8/feeds/contacts/default/full?alt=json',
            'method' => 'POST',
            'headers' => [
                 $this->_oauth->getTokenHeader(),
                'GData-Version: 3.0',
                'Content-Type: application/atom+xml',
            ],
            'data' => $xml,
        ];
        $response = Request::req($opt);
        var_dump($response);
    }
    
    public function createGroup() {
        var_dump("createGroup\n");
        $xml = $this->_createGroupXml();
            $opt = [
            'url' => 'https://www.google.com/m8/feeds/groups/default/full?alt=json',
            'method' => 'POST',
            'headers' => [
                 $this->_oauth->getTokenHeader(),
                'GData-Version: 3.0',
                'Content-Type: application/atom+xml',
            ],
            'data' => $xml,
        ];
        $response = Request::req($opt);
        if ($response['status'] !== 201) {
            throw new \Exception('Create new group '. $this->_contact['group'].' failed.');
        }
        
        $data = json_decode($response['response'], true);
        $this->_tGroupId = $data['entry']['id']['$t'];
        var_dump($this->_tGroupId);
        var_dump("create\n");
    }

    private function _createGroupXml() {
        $t = '<entry xmlns="http://www.w3.org/2005/Atom"
       xmlns:gd="http://schemas.google.com/g/2005"
       gd:etag="&quot;Rno4ezVSLyp7ImA9WxdTEUgNRQU.&quot;">
    <category scheme="http://schemas.google.com/g/2005#kind"
              term="http://schemas.google.com/g/2005#group"/>
    <title>%s</title>
        </entry>';
        return sprintf($t, $this->_contact['group']);
    }

    // заменить на xml 
    private function _createUserXml() {
        $t = '<atom:entry xmlns:atom="http://www.w3.org/2005/Atom"
        xmlns:gd="http://schemas.google.com/g/2005">
      <atom:category scheme="http://schemas.google.com/g/2005#kind"
        term="http://schemas.google.com/contact/2008#contact"/>
      <gd:name>
         <gd:givenName>%1$s</gd:givenName>
         <gd:familyName>%2$s</gd:familyName>
         <gd:additionalName>%3$s</gd:additionalName>
         <gd:fullName>%4$s</gd:fullName>     
      </gd:name>
      <gd:email label="Personal" address="%5$s"/>
     <gd:phoneNumber label="Personal">%6$s</gd:phoneNumber>
      <gContact:groupMembershipInfo href="%7$s"/>
    </atom:entry>';
        return sprintf($t,
                $this->_contact['firstname'],
                $this->_contact['surname'],
                $this->_contact['middlename'],
                sprintf('%s %s %s', $this->_contact['surname'], $this->_contact['firstname'], $this->_contact['middlename']),
                $this->_contact['email'],
                $this->_contact['phone'],
                $this->_tGroupId
            );
    }
    
}
