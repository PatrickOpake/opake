<?php

return [
	'default' => [
		'driver' => 'GD'
	],
	'default_inventory_logo' => '/i/default-logo.png',
	'default_settings' => [
		'allowed_mime_types' => [
			'image/jpeg',
			'image/png',
			'image/gif'
		],
		'allowed_size' => 5 * 1024 * 1024,
		'thumbnails' => [
			'default' => [
				'width' => 160,
				'height' => 160,
				//'accuracy' => 'fill', // нужно-ли приводить к точным размерам
			],
			'small' => [
				'width' => 100,
				'height' => 100,
				//'accuracy' => 'fill',
			],
			'tiny' => [
				'width' => 35,
				'height' => 35,
				//'accuracy' => 'fill',
			],
		],
	],
	'settings' => [
		'editor' =>  [
			'allowed_mime_types' => [
				'image/jpeg',
				'image/png',
				'image/gif'
			],
			'allowed_size' => 3 * 1024 * 1024,
			'thumbnails' => [

			]
		],
		'scan' => [
			'allowed_mime_types' => [
				'image/jpeg',
				'image/png',
				'image/gif'
			],
			'allowed_size' => 10 * 1024 * 1024,
			'thumbnails' => [

			]
		],
		'user' => [
			'allowed_mime_types' => [
				'image/jpeg',
				'image/png',
				'image/gif'
			],
			'allowed_size' => 5 * 1024 * 1024,
			'thumbnails' => [
				'default' => [
					'width' => 130,
					'height' => 130,
					'accuracy' => 'crop',
				],
				'tiny' => [
					'width' => 35,
					'height' => 35,
					'accuracy' => 'crop',
				]
			],
		]
	]
];

