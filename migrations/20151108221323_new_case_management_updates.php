<?php

use \Console\Migration\BaseMigration;

class NewCaseManagementUpdates extends BaseMigration
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
		ALTER TABLE `case` DROP `unused_loccards`;

		ALTER TABLE `case` ADD `time_start_in_fact` TIMESTAMP NULL AFTER `time_end`,
			ADD `time_end_in_fact` TIMESTAMP NULL AFTER `time_start_in_fact`;
        ");
	}
}
