<?php

use \Console\Migration\BaseMigration;

class UpdateOpReportTemplate extends BaseMigration
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
	    try {
			$this->query("
				ALTER TABLE `case_op_report_future_fields_template` ADD COLUMN `on_page` TINYINT(1) NULL DEFAULT '0' AFTER `active`;
				ALTER TABLE `case_op_report_fields_template` ADD COLUMN `on_page` TINYINT(1) NULL DEFAULT '0' AFTER `active`;
    				ALTER TABLE `case_op_report_future_fields_template` ADD COLUMN `list_value` TEXT NULL DEFAULT NULL;
    				ALTER TABLE `case_op_report_fields_template` ADD COLUMN `list_value` TEXT NULL DEFAULT NULL;
    				ALTER TABLE `case_op_report_future_fields_template` DROP `group_id`;
			");
	    } catch (\Exception $e) {
		    $this->writeln("on_page and list_value is exist");
	    }

	    $db = $this->getDb();
	    $db->begin_transaction();
	    try {
		    $rows = $this->getDb()->query('select')->table('case_op_report_future_fields_template')
			    ->fields('id', 'active')
			    ->execute();
		    foreach ($rows as $row) {
			    $this->getDb()->query('update')->table('case_op_report_future_fields_template')
				    ->data([
					    'on_page' => $row->active,
				    ])
				    ->where('id', $row->id)
				    ->execute();
		    }
		    $db->commit();
	    } catch (\Exception $e) {
		    $db->rollback();
		    $this->writeln("can't update case_op_report_future_fields_template");
		    throw $e;
	    }

	    $db->begin_transaction();
	    try {
		    $rows = $this->getDb()->query('select')->table('case_op_report_fields_template')
			    ->fields('id', 'active')
			    ->execute();
		    foreach ($rows as $row) {
			    $this->getDb()->query('update')->table('case_op_report_fields_template')
				    ->data([
					    'on_page' => $row->active,
				    ])
				    ->where('id', $row->id)
				    ->execute();
		    }
		    $db->commit();
	    } catch (\Exception $e) {
		    $db->rollback();
		    $this->writeln("can't update case_op_report_fields_template");
		    throw $e;
	    }
    }
}
