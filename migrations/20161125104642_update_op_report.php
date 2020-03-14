<?php

use \Console\Migration\BaseMigration;

class UpdateOpReport extends BaseMigration
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
    		ALTER TABLE `case_op_report_fields_template` DROP `is_custom`;
    		ALTER TABLE `case_op_report_future_fields_template` DROP `is_custom`;
    		ALTER TABLE `case_op_report_site_template` DROP `is_custom`;
    		");


	} catch (\Exception $e) {
		$this->writeln("cant drop is_custom");
	}

	try {
		$this->query("
    		ALTER TABLE `case_op_report_fields_template` ADD COLUMN `custom_value` MEDIUMTEXT NULL DEFAULT NULL;
    		ALTER TABLE `case_op_report_future_fields_template` ADD COLUMN `custom_value` MEDIUMTEXT NULL DEFAULT NULL;
    		");
	} catch (\Exception $e) {
		$this->writeln("custom_value is exist");
	}



    	$db = $this->getDb();
    	$db->begin_transaction();
	try {
		$rows = $this->getDb()->query('select')->table('case_op_report_custom_field_value')
			->fields('id', 'value', 'name', 'report_id')
			->execute();
		foreach ($rows as $row) {
			if($row->name) {
				$this->getDb()->query('update')->table('case_op_report_fields_template')
					->data([
						'custom_value' => $row->value,
					])
					->where('report_id', $row->report_id)
					->where('name', $row->name)
					->where('field', 'custom')
					->execute();
			}
		}
		$db->commit();
	} catch (\Exception $e) {
		$db->rollback();
		$this->writeln("can't update case_op_report_fields_template");
		throw $e;
	}


    	$db->begin_transaction();
	try {
		$rows = $this->getDb()->query('select')->table('case_op_report_future_custom_field_value')
			->fields('id', 'value', 'name', 'future_id')
			->execute();

		foreach ($rows as $row) {
			if($row->name) {
				$this->getDb()->query('update')->table('case_op_report_future_fields_template')
					->data([
						'custom_value' => $row->value,
					])
					->where('future_template_id', $row->future_id)
					->where('name', $row->name)
					->where('field', 'custom')
					->execute();
			}
		}
		$db->commit();
	} catch (\Exception $e) {
		$db->rollback();
		$this->writeln("can't update case_op_report_fields_template");
		throw $e;
	}

	    try {
	    	$this->query("
			DROP TABLE `case_op_report_custom_field_value`;
			DROP TABLE `case_op_report_future_custom_field_value`;
		");
	    } catch (\Exception $e) {
		    $this->writeln("can't drop case_op_report_custom_field_value");
	    }

    }
}
