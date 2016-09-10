<?php
namespace GoogleContacts\Oauth;
use GoogleContacts\Request\Request;

class Oauth {
    CONST GRANT_TYPE = 'urn:ietf:params:oauth:grant-type:jwt-bearer';
    private $_token = '';
    private $_token_type = '';
    private $_expires_in = 0;
    private $_time = 0;

    public function requestToken($assertion) {
        $opt = [
            'url' => 'https://www.googleapis.com/oauth2/v4/token',
            'headers' => ["Content-type: application/x-www-form-urlencoded"],
            "method" => 'POST',
            "data" => [
                'grant_type' => self::GRANT_TYPE,
                'assertion' => $assertion
            ]
        ];
        $response = Request::req($opt);
        if ($response['status'] == 200) {
            $this->setToken(json_decode($response['response'], true));
        } else {
            throw new \Exception('Oauth fail. Status: '.$response['status'].', response: '.$response['response']);
        }         
    }
    
    public function setToken($param) {
        if (!isset($param['access_token'])) 
            throw new \Exception ('Not defined: access_token');
        if (!isset($param['token_type'])) {
            throw new \Exception ('Not defined: token_type');
        }
        if (!isset($param['expires_in'])) {
            throw new \Exception ('Not defined: expires_in');
        }
        
        $this->_token = $param['access_token'];
        $this->_token_type = $param['token_type'];
        $this->_expires_in = $param['expires_in'];
        $this->_time = time();
        var_dump("Token status: ".$this->_token."\n");
    }
    
    public function getTokenHeader() {
        $this->_token_check();
        return 'Authorization: Bearer '.$this->_token;
    }
    
    private function _token_check() {
        if ($this->_time + $this->_expires_in < time()) {
            throw new \Exception('Token died!');
        }
    }
    
}
