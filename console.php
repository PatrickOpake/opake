<?php

ini_set('memory_limit', '1024M');

$root = __DIR__;
$loader = require $root . '/vendor/autoload.php';
$loader->add('', $root . '/classes/');

$pixie = new \Console\Application();
$pixie->bootstrap($root);
$method = $argv[1];
$params = array_slice($argv, 2);

$cli = new \Console\CLI($pixie);

//This is to avoid default HTML error rendering
try {
	call_user_func_array(array($cli, $method), $params);
} catch (Exception $e) {

	$pixie->logger->exception($e);

	echo 'ERROR: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();
	echo "\r\n";
	echo "Trace: \r\n";
	echo $e->getTraceAsString();
	echo "\r\n";

	exit(1);

}
