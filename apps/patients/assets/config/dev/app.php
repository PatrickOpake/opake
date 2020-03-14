<?php

return [
	'debugmode' => true,
	'log_dir' => null,
	'patient_portal_web' => 'http://patients.opake.local/',
	'share' => __DIR__ . '/../../../../common/public',
	'files' => array(
		'path' => '/uploads',
		'public' => '/common/uploads'
	),
	'protected_files' => array(
		'path' => __DIR__ . '/../../../../common/protected',
		'route' => '/file/view'
	),
];
