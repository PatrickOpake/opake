<?php

use \Console\Migration\BaseMigration;

class AddDragAndDropToTemplate extends BaseMigration
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
          ALTER TABLE `case_op_report_site_template` ADD COLUMN `sort` tinyint(4) NULL;
          ALTER TABLE `case_op_report_site_template` ADD COLUMN `show` VARCHAR (45) NULL;
          ALTER TABLE `case_op_report_custom_field` ADD COLUMN `sort` tinyint(4) NULL;
          ALTER TABLE `case_op_report_future_fields_template` ADD COLUMN `sort` tinyint(4) NULL;
          ALTER TABLE `case_op_report_future_fields_template` ADD COLUMN `show` VARCHAR (45) NULL;
          ALTER TABLE `case_op_report_fields_template` ADD COLUMN `sort` tinyint(4) NULL;
          ALTER TABLE `case_op_report_fields_template` ADD COLUMN `show` VARCHAR (45) NULL;
          ALTER TABLE `case_op_report_custom_fields_template` CHANGE `field_id` `field_id` INT(11) NULL;
          ALTER TABLE `case_op_report_custom_fields_template` ADD COLUMN `sort` tinyint(4) NULL;
          ALTER TABLE `case_op_report_custom_fields_template` ADD COLUMN `name` VARCHAR (255) NULL;

          ALTER TABLE `case_op_report_custom_field_value` CHANGE `field_id` `field_id` INT(11) NULL;
          ALTER TABLE `case_op_report_custom_field_value` ADD COLUMN `field_name` VARCHAR (255) NULL;
          ALTER TABLE `case_op_report_future_custom_field_value` ADD COLUMN `field_name` VARCHAR (255) NULL;
          ALTER TABLE `case_op_report_future_custom_field_value` CHANGE `field_id` `field_id` INT(11) NULL;
          ALTER TABLE `case_op_report_site_template` CHANGE `active` `active` TINYINT(1) NULL;
          ALTER TABLE `case_op_report_fields_template` CHANGE `active` `active` TINYINT(1) NULL;
          ALTER TABLE `case_op_report_future_custom_fields_template` ADD COLUMN `name` VARCHAR (255) NULL;

          DROP TABLE `case_op_report_custom_field`;
          DROP TABLE `case_op_report_custom_fields_template`;
          DROP TABLE `case_op_report_future_custom_fields_template`;


        ");
    }
}
