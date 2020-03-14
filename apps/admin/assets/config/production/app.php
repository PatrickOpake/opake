<?php

return [
	'debugmode' => false,
	'web' => 'https://opake.com/',
	'patient_portal_web' => 'https://patients.opake.com',
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
		'base_url' => 'http://opake.rokolabs.local/external/v1/',
		'api_key' => 'tuSD2h/k7y/BaC2OD9l2m2RknUCdgDQlfnk2gpFPkO0=',
		'api_master_key' => 'rylE3pB1Yy2p2lXm4C0iF/V6yC4Yu7ocnRMTv5aBFlw='
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
			'login' => 'BJN81756',
			'password' => 'opake0912',
			'host' => 'secureftp.navicure.com',
		],
		'soap' => [
			'login' => 'BJN81756',
			'password' => 'opake0912',
			'wsdl_url' => 'https://ww3.navicure.com:7000/webservices/NavicureSubmissionService?WSDL',
			'endpoint_url' => 'https://ww3.navicure.com:7000/webservices/NavicureSubmissionService',
		]
	],
	'scrypt_sfax_api' => [
		'disable_polling' => false,
		'endpoint_url' => 'https://api.sfaxme.com/api/',
		'user_name' => 'opake_prod_api',
		'api_key' => '322ACD685EF74DDAA364A9EF260BF90A',
		'encryption_key' => '*3^AjiP_8(59m!FLWXE*^6Ww(p_#hGjd',
		'encryption_init_vector' => 'x49e*wJVXr8BrALE'
	]
];

