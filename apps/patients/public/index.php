<?php

$maintenancePath = '../../common/public/maintenance.html';
if (file_exists($maintenancePath)) {
	http_response_code(503);
	echo file_get_contents($maintenancePath);
	exit;
}


$root = __DIR__.'/../../../';
$loader = require($root . '/vendor/autoload.php');

$pixie = new \OpakePatients\Application();
$pixie->bootstrap($root)->handle_http_request();
