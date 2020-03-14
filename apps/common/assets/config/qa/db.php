<?php

return array(
	'default' => array(
		'user' => 'opake_qa',
		'password' => 'ecabe4bfb391ed4966e3',
		'driver' => 'PDO',
		'connection' => 'mysql:host=e8163bcc.db.healthcareblocks.com;dbname=opake_qa',
		'mysql_ssl_ca' => '/ssl/hcb_ca.pem',
		'mysql_init_command' => 'SET sql_mode=""',
	),
);
