<?php

use \Console\Migration\BaseMigration;

class AddGroupField extends BaseMigration
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
            CREATE TABLE IF NOT EXISTS `case_op_report_field_group` (
                    `id` int(11) NOT NULL,
                    `name` VARCHAR (255) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ALTER TABLE `case_op_report_field_group` ADD PRIMARY KEY (`id`);
                ALTER TABLE `case_op_report_field_group` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

            INSERT INTO `case_op_report_field_group` (name) VALUES ('Case Information');
            INSERT INTO `case_op_report_field_group` (name) VALUES ('Descriptions');
            INSERT INTO `case_op_report_field_group` (name) VALUES ('Materials');
            INSERT INTO `case_op_report_field_group` (name) VALUES ('Conclusions');
            INSERT INTO `case_op_report_field_group` (name) VALUES ('Follow Up');

            ALTER TABLE `case_op_report_site_template` ADD COLUMN `group_id` int(11) NULL;
            ALTER TABLE `case_op_report_custom_field` ADD COLUMN `group_id` int(11) NULL;

        ");
    }
}
