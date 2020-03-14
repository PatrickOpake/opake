<?php

return array(
	'default' => array(
		'user' => 'opake_staging',
		'password' => 'f3af9eb019ffe748733c',
		'driver' => 'PDO',
		'connection' => 'mysql:host=e8163bcc.db.healthcareblocks.com;dbname=opake_staging',
		'mysql_ssl_ca' => '/ssl/hcb_ca.pem',
		'mysql_init_command' => 'SET sql_mode=""',
	),
);
