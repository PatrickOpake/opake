<?php

return [
	'debugmode' => false,
	'log_dir' => '/data/log/opake',
	'patient_portal_web' => 'https://patients.opake.com/',
	'share' => '/data/opake/public',
	'files' => array(
		'path' => '/uploads',
		'public' => '/uploads'
	),
	'protected_files' => array(
		'path' => '/data/opake/protected/uploads',
		'route' => '/file/view'
	),
];
