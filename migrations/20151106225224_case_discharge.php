<?php

use \Console\Migration\BaseMigration;

class CaseDischarge extends BaseMigration
{
	/**
	 * Change Method.
	 *
	 * Write your reversible migrations using this method.
	 *
	 * More information on writing migrations is available here:
	 * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
	 *
	 * The following commands can be used in this method and Phinx will
	 * automatically reverse them when rolling back:
	 *
	 *    createTable
	 *    renameTable
	 *    addColumn
	 *    renameColumn
	 *    addIndex
	 *    addForeignKey
	 *
	 * Remember to call "create()" or "update()" and NOT "save()" when working
	 * with the Table class.
	 */
	public function change()
	{
		$this->query("
		CREATE TABLE IF NOT EXISTS `case_discharge` (
		`id` int(11) NOT NULL,
		  `case_id` int(11) NOT NULL,
		  `path` varchar(255) NOT NULL,
		  `uploaded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		ALTER TABLE `case_discharge` ADD PRIMARY KEY (`id`), ADD KEY `case_id` (`case_id`);
		ALTER TABLE `case_discharge` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");
	}
}
