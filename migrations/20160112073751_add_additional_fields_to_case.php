<?php

use \Console\Migration\BaseMigration;

class AddAdditionalFieldsToCase extends BaseMigration
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
            CREATE TABLE IF NOT EXISTS `case_other_staff` (
                    `staff_id` int(11) NOT NULL,
                    `case_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_dictated_by`
                    ADD UNIQUE KEY `uni` (`staff_id`,`case_id`);
        ");
	}
}
