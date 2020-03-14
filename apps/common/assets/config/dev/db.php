<?php

return [
	'default' => [
		'user' => 'opake',
		'password' => 'opake',
		//'user' => 'root',
		//'password' => 'rootpass',
		'driver' => 'PDO',
		'connection' => 'mysql:host=localhost;dbname=opake',
		//'connection' => 'mysql:host=localhost;dbname=opake_qa',
		'mysql_init_command' => 'SET sql_mode=""',
		'profiler' => false
	],
];

