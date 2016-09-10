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

    /**
     * Принимаем php массив json файла из console google
     * @param array $config
     * @throws Exception
     */
    public function __construct($config = null) {
        if (is_null($config))
            throw new Exception ('Config not found!');
        $this->_config = $config;
        $this->_oauth = new Oauth();
        $this->_preparePayload();
    }
    
    private function _preparePayload() {
        $this->_payload = [
            'iss' => $this->_config['client_email'], // емайл сервис аккаунта
//            'sub' => 'aleksey.exe@gmail.com', // { "error": "unauthorized_client", "error_description": "Unauthorized client or scope in request." }
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
            'url' => 'https://www.google.com/m8/feeds/contacts/default/full',
            'method' => 'GET',
            'headers' => [
                $this->_oauth->getTokenHeader(),
                'GData-Version: 3.0',
            ],
        ];
        
        $response = Request::req($opt);
        var_dump($response);
    }
    
}
