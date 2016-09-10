<?php

require_once('/jwt/JWT.php');
require_once('./GoogleContacts.php');
require_once('./Request.php');
require_once('./Oauth.php');

use GoogleContacts\Service\Service;

$conf = json_decode(file_get_contents('./conf.json'), TRUE);

$g = new Service($conf);
$g->getToken();
$g->testContacts();