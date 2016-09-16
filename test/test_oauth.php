<?php

require_once '../jwt/JWT.php';
require_once '../lib/credentials.php';
require_once '../lib/oauth.php';
require_once '../lib/request.php';
require_once '../lib/response.php';

use GoogleContacts\Credentials;
use GoogleContacts\OAuth;

$service_account_json_key = file_get_contents('../conf.json');
$target_user_email = 'aleha@coderip.ru';
$credentials = new Credentials($service_account_json_key, $target_user_email);

$oauth = new OAuth;
$oauth->setCredentials($credentials);

$token = $oauth->getToken();

var_dump($token);