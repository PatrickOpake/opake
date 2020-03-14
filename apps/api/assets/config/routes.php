<?php

return [
	'inventory' => [
		'/items(/<action>)',
		[
			'controller' => 'inventory',
			'action' => 'list'
		]
	],
	'save/card' => [
		'/save/card(/<action>)',
		[
			'controller' => 'Save\Card'
		]
	],
	'default' => [
		'(/<controller>(/<action>(/<id>)))',
		[
			'controller' => 'user',
			'action' => 'empty'
		]
	]
];
