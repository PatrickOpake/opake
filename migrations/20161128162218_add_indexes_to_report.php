<?php

use \Console\Migration\BaseMigration;

class AddIndexesToReport extends BaseMigration
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
		ALTER TABLE `case_op_report_fields_template` ADD INDEX `CASE_REPORT_TEMPLATE_SORT` (`sort`);
		ALTER TABLE `case_op_report_fields_template` ADD INDEX `CASE_REPORT_TEMPLATE_REPORT_ID` (`report_id`);
		
		ALTER TABLE `case_op_report_future_fields_template` ADD INDEX `CASE_REPORT_TEMPLATE_FUTURE_SORT` (`future_template_id`);
		ALTER TABLE `case_op_report_future_fields_template` ADD INDEX `CASE_REPORT_TEMPLATE__FUTURE_REPORT_ID` (`sort`);
	");
    }
}
