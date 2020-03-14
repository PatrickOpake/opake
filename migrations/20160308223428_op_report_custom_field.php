<?php

use \Console\Migration\BaseMigration;

class OpReportCustomField extends BaseMigration
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
                CREATE TABLE IF NOT EXISTS `case_op_report_custom_field_value` (
                    `id` int(11) NOT NULL,
                    `report_id` int(11) NOT NULL,
                    `field_id` int(11) NOT NULL,
                    `value` TEXT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ALTER TABLE `case_op_report_custom_field_value` ADD PRIMARY KEY (`id`);
                ALTER TABLE `case_op_report_custom_field_value` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

                CREATE TABLE IF NOT EXISTS `case_op_report_custom_field` (
                    `id` int(11) NOT NULL,
                    `organization_id` int(11) NOT NULL,
                    `name` VARCHAR(255) NOT NULL,
                    `type` VARCHAR(40) NULL,
                    `active` tinyint(1) NULL DEFAULT '0'
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ALTER TABLE `case_op_report_custom_field` ADD PRIMARY KEY (`id`);
                ALTER TABLE `case_op_report_custom_field` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

                CREATE TABLE IF NOT EXISTS `case_op_report_future_custom_field_value` (
                    `id` int(11) NOT NULL,
                    `future_id` int(11) NOT NULL,
                    `field_id` int(11) NOT NULL,
                    `value` TEXT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ALTER TABLE `case_op_report_future_custom_field_value` ADD PRIMARY KEY (`id`);
                ALTER TABLE `case_op_report_future_custom_field_value` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

		");
	}
}
