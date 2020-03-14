<?php

return [
	'debugmode' => true,
	'version' => '1.2.164',
	'timezone' => 'America/New_York',
	'contact_email' => 'hello@opake.com',
	'session' => [
		'expires' => 1800,
		'gc_maxlifetime' => 1800,
		'save_handler' => 'memcache',
		'save_path' => 'tcp://localhost:11211'
	],
	'share' => __DIR__ . '/../../../common/public',
	'files' => array(
		'path' => '/uploads',
		'public' => '/common/uploads'
	),
	'protected_files' => array(
		'path' => __DIR__ . '/../../../common/protected',
		'route' => '/file/view'
	),
	'inactivity_reminder' => [
		'enabled' => true,
		'logoutTime' => 1800
	],
	'password_change_reminder' => [
		'enabled' => true,
		'days_since_last_change' => 120,
		'last_passwords_count' => 3,
		'salt' => '84428c6e0f727d85ad7da-c56a38a891a15005174-4457b57d0e87e42'
	],

];
