<?php

require_once '../lib/request.php';
require_once '../lib/response.php';

use GoogleContacts\Request;

$url = 'http://php.net/manual/en/ref.curl.php';

$request = new Request($url, ["Content-type: application/x-www-form-urlencoded"]);

$response = $request
	->setHeaders(["User-Agent: mozilla-hende-hoch"])
	->setBody('hello')
	->execute();

var_dump($response->getStatusCode());