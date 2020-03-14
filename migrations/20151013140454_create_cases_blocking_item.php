<?php

use \Console\Migration\BaseMigration;

class CreateCasesBlockingItem extends BaseMigration
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
            CREATE TABLE IF NOT EXISTS `case_blocking_item` (
              `id` int(11) NOT NULL,
              `organization_id` int(11) NOT NULL,
              `blocking_id` int(11) NOT NULL,
              `start` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
              `end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            ALTER TABLE `case_blocking_item`
              ADD PRIMARY KEY (`id`);

            ALTER TABLE `case_blocking_item`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");
	}
}
