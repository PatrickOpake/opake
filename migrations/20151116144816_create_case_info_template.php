<?php

use \Console\Migration\BaseMigration;

class CreateCaseInfoTemplate extends BaseMigration
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
                CREATE TABLE IF NOT EXISTS `case_info_template` (
		  `id` int(11) NOT NULL,
		  `organization_id` int(11) NOT NULL,
		  `field` varchar(255) NOT NULL,
		  `active` TINYINT(1) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		ALTER TABLE `case_info_template` ADD PRIMARY KEY (`id`), ADD KEY `organization_id` (`organization_id`);
		ALTER TABLE `case_info_template` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

		ALTER TABLE `case` ADD `co_surgeon` VARCHAR(255) NULL;
		ALTER TABLE `case` ADD `supervising_surgeon` VARCHAR(255) NULL;
		ALTER TABLE `case` ADD `first_assistant_surgeon` VARCHAR(255) NULL;
		ALTER TABLE `case` ADD `assistant` VARCHAR(255) NULL;
		ALTER TABLE `case` ADD `anesthesiologist` VARCHAR(255) NULL;
		ALTER TABLE `case` ADD `dictated_by` VARCHAR(255) NULL;
        ");
	}
}
