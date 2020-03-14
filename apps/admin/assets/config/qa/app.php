<?php

return [
	'debugmode' => false,
	'web' => 'http://qa.opake.com/',
	'patient_portal_web' => 'http://patients-qa.opake.com',
	'share' => '/data/opake/public',
	'log_dir' => '/data/log/opake/',
	'files' => array(
		'path' => '/uploads',
		'public' => '/uploads'
	),
	'protected_files' => array(
		'path' => '/data/opake/protected/uploads',
		'route' => '/file/view'
	),
	'timezone' => 'America/New_York',
	'export' => [
		'pdf' => '/usr/bin/wkhtmltopdf-xvfb.sh'
	],
	'templates' => [
		'master' => [
			'charges' => '/apps/admin/docs/CDM_Template.xlsx',
			'inventory' => '/apps/admin/docs/Item_Master_Template.xlsx',
		],
		'pref_card' => '/apps/admin/docs/Preference_Card_Template.xlsx',
		'inventory_report' => '/apps/admin/docs/Inventory_Report_Template.xlsx',
		'insurance_payor' => '/apps/admin/docs/Insurance_DB.xlsx'

	],
	'rokomobi_api' => [
		'base_url' => 'https://rmsws.qa.rokolabs.com/external/v1/',
		'api_key' => 'mo+TkOC4OBJRUlVKhKLZcLr81WUdJ+rTeHUrSn5aMu4=',
		'api_master_key' => 'oIaZdwMiUy00rtGxQDkTBnIPXf7wOTGA1IjN7aV/jCo='
	],
	'dragon_dictation' => [
		'enable' => true,
		'application_name' => 'OpakeDev'
	],
	'twilio_api' => [
		'account_sid' => 'ACb83bef4831d34364d2429711d2cc6d92',
		'auth_token' => '0451afcebbba6c73c36dc857e1ec226a',
		'phone_from' => '+16463742229',
	],
	'imagemagick_convert_command' => 'convert',
	'navicure_api' => [
		'sftp' => [
			'login' => 'qYRg171V',
			'password' => 'opake0303',
			'host' => 'secureftp.navicure.com',
			'disable_polling' => false
		],
		'soap' => [
			'login' => 'qYRg171V',
			'password' => 'opake0303',
			'wsdl_url' => 'https://ww3.navicure.com:7000/webservices/NavicureSubmissionService?WSDL',
			'endpoint_url' => 'https://ww3.navicure.com:7000/webservices/NavicureSubmissionService',
		]
	],
	'scrypt_sfax_api' => [
		'disable_polling' => false,
		'endpoint_url' => 'https://api.sfaxme.com/api/',
		'user_name' => 'opake_qa_api',
		'api_key' => '4802EC6EA1EF4560B7B5D5003A609F1C',
		'encryption_key' => 'W^CEt9MqrvWkqkTIN!BAS(qwmNFI5pFt',
		'encryption_init_vector' => 'x49e*wJVXr8BrALE'
	]
];

