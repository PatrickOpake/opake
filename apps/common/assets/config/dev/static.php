<?php

return [
	'less' => [
		'check_files' => true,
		'compiled_path' => '/public/css'
	],
	'minify' => [
		'enable' => false,
		'base_path' => 'public/',
		'common' => [
			'path' => 'apps/common/public',
			'web' => '/common',
		],
		'cache' => [
			'path' => 'public/tmp/minify',
			'web' => '/tmp/minify',
		],
		'ttl' => 86400
	]
];
