<?php

use \Console\Migration\BaseMigration;

class TruncateReports extends BaseMigration
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
        $this->query('
            TRUNCATE case_op_report_site_template;
            TRUNCATE case_op_report_future_fields_template;
            TRUNCATE case_op_report_future_custom_field_value;
            TRUNCATE case_op_report_fields_template;
            TRUNCATE case_op_report_custom_field_value;
        ');
    }
}
