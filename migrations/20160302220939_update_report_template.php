<?php

use \Console\Migration\BaseMigration;

class UpdateReportTemplate extends BaseMigration
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
            ALTER TABLE `case_op_report_fields_template` ADD `staff` tinyint(1) NOT NULL DEFAULT 1;
            ALTER TABLE `case_op_report_future_fields_template` ADD `staff` tinyint(1) NOT NULL DEFAULT 1;
            ALTER TABLE `case_op_report_template`
              ADD `name` VARCHAR(255) NULL,
              ADD `type` VARCHAR(40) NULL;

        ");
	}
}
