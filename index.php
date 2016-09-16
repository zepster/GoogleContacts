<?php

require_once 'jwt/JWT.php';
require_once 'lib/credentials.php';
require_once 'lib/client.php';
require_once 'lib/contact_data.php';
require_once 'lib/oauth.php';
require_once 'lib/result.php';
require_once 'lib/request.php';
require_once 'lib/response.php';
require_once 'lib/ContactEntry.php';
require_once 'lib/GroupEntry.php';

use GoogleContacts\Credentials;
use GoogleContacts\Client;
use GoogleContacts\ContactData;

// ключ сервисного аккаунта из файла
$service_account_json_key = file_get_contents('./conf.json');

// email пользователя в домене Google Apps, с чьими контактами работаем
$target_user_email = 'aleha@coderip.ru';

// данные для авторизации от имени пользователя домена
$credentials = new Credentials($service_account_json_key, $target_user_email);

$client = new Client;
$client->setCredentials($credentials);

// данные сохраняемого контакта (см. конструктор)
$contact_data = new ContactData(
	'Алексей',
	'Смирнов',
	'smirnov@test.com',
	'3123-1231231',
	'izhevsj'
);

// работаем...
$result = $client->addContact($contact_data);

var_dump($result);
