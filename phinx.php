<?php

$root = __DIR__;
$loader = require($root . '/vendor/autoload.php');

$pixie = new \Console\Application();
$pixie->bootstrap($root);

$pdo = $pixie->db->get()->conn;

\Console\Migration\BaseMigration::initPixie($pixie);

return [
	'paths' => [
		'migrations' => '%%PHINX_CONFIG_DIR%%/migrations'
	],
	'migration_base_class' => '\Console\Migration\BaseMigration',
	'environments' => [
		'default_migration_table' => 'migrations',
		'default_database' => 'default',
		'default' => [
			'name' => $pdo->query('select database()')->fetchColumn(),
			'connection' => $pixie->db->get()->conn
		]
	]
];
