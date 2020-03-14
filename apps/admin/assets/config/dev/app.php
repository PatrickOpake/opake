<?php

return [
	'debugmode' => true,
	'web' => 'http://opake.local/',
	'log_dir' => null,
	'patient_portal_web' => 'http://patients.opake.local',
	'share' => __DIR__ . '/../../../../common/public',
	'files' => array(
		'path' => '/uploads',
		'public' => '/common/uploads'
	),
	'protected_files' => array(
		'path' => __DIR__ . '/../../../../common/protected',
		'route' => '/file/view'
	),
	'timezone' => 'America/New_York',
	'marketing' => 'http://opake.local/auth/login',
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
		'base_url' => 'https://rmsws.dev.rokolabs.com/external/v1/',
		'api_key' => '3ZwI1ASdsNliJ8mUPpSuliCf0zvCioHIZWPdtMv6mPo=',
		'api_master_key' => 'TzPDSqKELOE3PectwevIajPET3d2Db6+LH7U9rFfvmE='
	],
	'inactivity_reminder' => [
		'enabled' => true,
		'logoutTime' => 1800
	],
	'imagemagick_convert_command' => 'convert',
	'navicure_api' => [
		'sftp' => [
			'login' => 'qYRg171V',
			'password' => 'opake0303',
			'host' => 'secureftp.navicure.com',
			'disable_polling' => false,
			'disable_removing' => false,
			'disable_sending' => true
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
		'user_name' => 'opake_dev_api',
		'api_key' => 'DB8DF321467148A281A58C7D153219DE',
		'encryption_key' => 'c9hVjN6RuW!EyNx$7Iu4e6Bsr(a#DWC(',
		'encryption_init_vector' => 'x49e*wJVXr8BrALE'
	],
	'coding' => [
		'forms_use_template' => true
	]
];
