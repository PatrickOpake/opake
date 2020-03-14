<?php

$root = __DIR__ . '/../../../';
$loader = require($root . '/vendor/autoload.php');

$pixie = new \OpakeApi\Application();
$pixie->bootstrap($root)->handle_http_request();
