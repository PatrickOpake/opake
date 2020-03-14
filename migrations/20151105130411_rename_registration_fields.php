<?php

use \Console\Migration\BaseMigration;

class RenameRegistrationFields extends BaseMigration
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
            ALTER TABLE `case_registration`  CHANGE COLUMN `pd_first_name` `first_name` varchar(255) DEFAULT NULL;
             ALTER TABLE `case_registration` CHANGE COLUMN `pd_middle_name` `middle_name` varchar(255) DEFAULT NULL;
             ALTER TABLE `case_registration` CHANGE COLUMN `pd_last_name` `last_name` varchar(255) DEFAULT NULL;
             ALTER TABLE `case_registration` CHANGE COLUMN `pd_dob` `dob` DATE NULL DEFAULT NULL;
             ALTER TABLE `case_registration` CHANGE COLUMN `pd_gender` `gender` TINYINT(4) NULL DEFAULT NULL;
             ALTER TABLE `case_registration` CHANGE COLUMN `pd_race` `race` TINYINT(4) NULL DEFAULT NULL;
        ");

	}
}
