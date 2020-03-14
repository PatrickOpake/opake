<?php

use \Console\Migration\BaseMigration;

class ChangeOperativeReportApi extends BaseMigration
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
        DROP TABLE `case_op_report_fields_template`;
        CREATE TABLE IF NOT EXISTS `case_op_report_fields_template` (
                `id` int(11) NOT NULL,
                `organization_id` int(11) NOT NULL,
                `case_id` INT(11) NULL,
                `field` varchar(255) NULL,
                `name` varchar(255) NULL,
                `type` varchar(255) NULL,
                `group_id` INT(11) NULL,
                `active` TINYINT(1) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            ALTER TABLE `case_op_report_fields_template` ADD PRIMARY KEY (`id`);
            ALTER TABLE `case_op_report_fields_template` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

            DROP TABLE `case_op_report_future_fields_template`;
        CREATE TABLE IF NOT EXISTS `case_op_report_future_fields_template` (
                `id` int(11) NOT NULL,
                `organization_id` int(11) NOT NULL,
                `future_template_id` INT(11) NULL,
                `field` varchar(255) NULL,
                `name` varchar(255) NULL,
                `type` varchar(255) NULL,
                `group_id` INT(11) NULL,
                `active` TINYINT(1) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            ALTER TABLE `case_op_report_future_fields_template` ADD PRIMARY KEY (`id`);
            ALTER TABLE `case_op_report_future_fields_template` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

            ALTER TABLE `case_op_report_custom_fields_template` ADD COLUMN `group_id` int(11) NULL;
            ALTER TABLE `case_op_report_future_custom_fields_template` ADD COLUMN `group_id` int(11) NULL;
        ");
    }
}
