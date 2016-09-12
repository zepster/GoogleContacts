<?php

require_once('./jwt/JWT.php');
require_once('./GoogleContacts.php');
require_once('./Request.php');
require_once('./Oauth.php');

use GoogleContacts\Service\Service;

$conf = json_decode(file_get_contents('./conf.json'), TRUE);
$user = 'aleha@coderip.ru';
$json = [
    'firstname' => 'dAlehaTest1',
    'surname' => 'SamirnovTest2',
    'middlename' => 'aSergeei3',
    'email' => 'alehda2a@test.com',
    'phone' => '3123122221',
    'group' => 'zew'
];

$g = new Service($conf, $user, $json);
$g->getToken();
if (!$g->groupExists()) { 
    $g->createGroup();
}
if (!$g->contactExist()) {
    $g->createContact();
}
