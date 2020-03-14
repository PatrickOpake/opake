<?php

use Phinx\Migration\AbstractMigration;

class CreateCaseSettingTable extends AbstractMigration
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
                CREATE TABLE IF NOT EXISTS `case_setting` (
                    `id` int(11) NOT NULL,
                    `organization_id` int(11) NOT NULL,
                    `block_timing` int(11) NOT NULL,
                    `block_overwrite` tinyint(1) NOT NULL DEFAULT '0'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ALTER TABLE `case_setting` ADD PRIMARY KEY (`id`);
                ALTER TABLE `case_setting` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

                ALTER TABLE `user` ADD `case_color` VARCHAR(255) NULL DEFAULT NULL;
		");
	}
}
