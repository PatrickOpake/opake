<?php

return array(
	'default' => array(
		'user' => 'empty',
		'password' => 'empty',
		'driver' => 'PDO',
		'connection' => 'mysql:host=db.internal;dbname=opake',
		'mysql_ssl_ca' => '/ssl/hcb_ca.pem',
		'mysql_init_command' => 'SET sql_mode=""',
	),
);

