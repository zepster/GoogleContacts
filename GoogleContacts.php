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
    
    //тестовая группа
    private $_tGroupId = 'http://www.google.com/m8/feeds/groups/aleha%40coderip.ru/base/1fbd2e78f9631d3';

    /**
     * Принимаем php массив json файла из console google
     * @param array $config
     * @throws Exception
     */
    public function __construct($config = null, $user) {
        if (is_null($config))
            throw new Exception ('Config not found!');
        $this->_config = $config;
        $this->_targetUser = $user;
        $this->_oauth = new Oauth();
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
        $opt = [
            'url' => 'https://www.google.com/m8/feeds/groups/default/full?alt=json',
            'method' => 'GET',
            'headers' => [
                $this->_oauth->getTokenHeader(),
                'GData-Version: 3.0',
            ]
        ];
        $response = Request::req($opt);
        if ($response['status'] !== 200) {
            throw new Exception('Function getGroupId return fail http status');
        }
        $data = json_decode($response['response'], true);
        if (isset($data['feed']) && $data['feed']['entry']) {
            foreach ($data['feed']['entry'] as $gr) {
                if ($gr['title']['$t'] == 'test') {
                    $this->_tGroupId = $gr['id']['$t'];
                    continue;
                }
            }
        }
    }
    
    public function contactExist() {        
        $opt = [
            'url' => 'https://www.google.com/m8/feeds/contacts/default/full?alt=json&group='.$this->_tGroupId.'&q=test',
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
        var_dump($data);
    }
    
    public function createContact() {
        $xml = $this->_createUserXml();
        $opt = [
            'url' => 'https://www.google.com/m8/feeds/contacts/default/full',
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
    
    private function _createUserXml() {
        return '<atom:entry xmlns:atom="http://www.w3.org/2005/Atom"
    xmlns:gd="http://schemas.google.com/g/2005">
  <atom:category scheme="http://schemas.google.com/g/2005#kind"
    term="http://schemas.google.com/contact/2008#contact"/>
  <gd:name>
     <gd:givenName>Elizabeth</gd:givenName>
     <gd:familyName>Bennet</gd:familyName>
     <gd:fullName>Elizabeth Bennet</gd:fullName>
  </gd:name>
  <atom:content type="text">Notes</atom:content>
  <gd:email rel="http://schemas.google.com/g/2005#work"
    primary="true"
    address="liz@gmail.com" displayName="E. Bennet"/>
  <gd:email rel="http://schemas.google.com/g/2005#home"
    address="liz@example.org"/>
  <gd:phoneNumber rel="http://schemas.google.com/g/2005#work"
    primary="true">
    (206)555-1212
  </gd:phoneNumber>
  <gd:phoneNumber rel="http://schemas.google.com/g/2005#home">
    (206)555-1213
  </gd:phoneNumber>
  <gd:im address="liz@gmail.com"
    protocol="http://schemas.google.com/g/2005#GOOGLE_TALK"
    primary="true"
    rel="http://schemas.google.com/g/2005#home"/>
  <gd:structuredPostalAddress
      rel="http://schemas.google.com/g/2005#work"
      primary="true">
    <gd:city>Mountain View</gd:city>
    <gd:street>1600 Amphitheatre Pkwy</gd:street>
    <gd:region>CA</gd:region>
    <gd:postcode>94043</gd:postcode>
    <gd:country>United States</gd:country>
    <gd:formattedAddress>
      1600 Amphitheatre Pkwy Mountain View
    </gd:formattedAddress>
  </gd:structuredPostalAddress>
  <gContact:groupMembershipInfo href="http://www.google.com/m8/feeds/groups/aleha%40coderip.ru/base/1fbd2e78f9631d3"/>
</atom:entry>';
    }
    
}
