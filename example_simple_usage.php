<?php

require_once 'lib/service.php';
require_once 'lib/contact.php';
require_once 'lib/result.php';

use GoogleContacts\Service;
use GoogleContacts\Contact;

// читаем ключ сервисного аккаунта из файла
$service_account_json_key = file_get_contents('./conf.json');
$service = new Service($service_account_json_key);

// email пользователя в домене Google Apps, с чьими контактами работаем
$target_user_email = 'aleha@coderip.ru';

// данные сохраняемого контакта (см. конструктор класса ContactData)
$contact_data = new Contact(
	'dAlehaTest21',
	'SamirnovTest2',
	'alehda2a@test.com',
	'3123122221',
	'zew'
);

// работаем...
$result = $service->addUserContact($target_user_email, $contact_data);
