<?php

require_once('./jwt/JWT.php');
require_once('./GoogleContacts.php');
require_once('./Request.php');
require_once('./Oauth.php');

use GoogleContacts\Service\Service;

$conf = json_decode(file_get_contents('./conf.json'), TRUE);
$user = 'aleha@coderip.ru';
$json = [
    'name' => 'AlehaTest',
    'surname' => 'SmirnovTest',
    'email' => 'aleha@test.com',
    'phone' => '31231221',
    'group' => 'test'
];

$g = new Service($conf, $user);
$g->getToken();
$g->groupExists();
$g->contactExist();
