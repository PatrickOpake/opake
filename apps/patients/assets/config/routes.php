<?php

return [
	'api/choice' => [
		'/api/choice(/<action>)',
		[
			'controller' => 'Api\Choice'
		]
	],
	'api/geo' => [
		'/api/geo(/<action>)',
		[
			'controller' => 'Api\Geo'
		]
	],
	'api/patients' => [
		'/api/patients(/<action>(/<id>))',
		[
			'controller' => 'Api\Patients'
		]
	],
	'api/auth' => [
		'/api/auth(/<action>)',
		[
			'controller' => 'Api\Auth'
		]
	],
	'api/appointments' => [
		'/api/appointments(/<action>)',
		[
			'controller' => 'Api\Appointments'
		]
	],
	'api/appointments/alerts' => [
		'/api/appointments/alerts(/<action>)',
		[
			'controller' => 'Api\Appointments\Alerts'
		]
	],
	'api/appointments/forms/pre-operative' => [
		'/api/appointments/forms/pre-operative(/<action>)',
		[
			'controller' => 'Api\Appointments\Forms\PreOperative'
		]
	],
	'api/appointments/forms/influenza' => [
		'/api/appointments/forms/influenza(/<action>)',
		[
			'controller' => 'Api\Appointments\Forms\Influenza'
		]
	],
	'api/documents' => [
		'/api/documents(/<action>)',
		[
			'controller' => 'Api\Documents'
		]
	],
	'api/insurances' => [
		'/api/insurances(/<action>)',
		[
			'controller' => 'Api\Insurances'
		]
	],
	'image/ajax/' => [
		'/image/ajax(/<action>)',
		[
			'controller' => 'Api\Image'
		]
	],
	'file/' => [
		'/file(/<action>)',
		[
			'controller' => 'File'
		]
	],
	'default' => [
			function($url) {
				return [
					'portal_alias' => $url ? explode('/', $url)[1] : ''
				];
			},
			[
				'controller' => 'Index',
				'action' => 'index'
			]
		]
	];
	