<?php

return [
	'debugmode' => false,
	'patient_portal_web' => 'http://opakepatients.dev.rokolabs.com/',
	'share' => 'd:/rk/opake/shared',
	'files' => array(
		'path' => '/uploads',
		'public' => '/uploads'
	),
	'protected_files' => array(
		'path' => 'd:/rk/opake/shared/protected',
		'route' => '/file/view'
	),
];
