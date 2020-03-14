<?php

return [
	'session' => [
		'expires' => 1800,
		'gc_maxlifetime' => 1800,
		'save_handler' => 'memcache',
		'save_path' => 'tcp://127.0.0.1:11211'
	],
];
