<?php

require_once('./jwt/JWT.php');
require_once('./GoogleContacts.php');
require_once('./Request.php');
require_once('./Oauth.php');

use GoogleContacts\Service\Service;

$conf = json_decode(file_get_contents('./conf.json'), TRUE);
$user = 'aleha@coderip.ru';
$json = [
    'firstname' => 'dAlehaTest21',
    'surname' => 'SamirnovTest2',
    'middlename' => 'aSerdsageei3',
    'email' => 'alehda2a@test.com',
    'phone' => '3123122221',
    'group' => 'zew'
];

$g = new Service($conf, $user, $json);
$g->go();
