<?php

use \Console\Migration\BaseMigration;

class RefactoringReport extends BaseMigration
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
		ALTER TABLE `case_op_report_custom_field_value` ADD COLUMN `name` VARCHAR (255) NULL DEFAULT NULL;
		ALTER TABLE `case_op_report_future_custom_field_value` ADD COLUMN `name` VARCHAR (255) NULL DEFAULT NULL;
	    	ALTER TABLE `case_op_report_fields_template` ADD COLUMN `is_custom` TINYINT(1) NOT NULL DEFAULT 0;
		ALTER TABLE `case_op_report_site_template` ADD COLUMN `is_custom` TINYINT(1) NOT NULL DEFAULT 0;
		ALTER TABLE `case_op_report_future_fields_template` ADD COLUMN `is_custom` TINYINT(1) NOT NULL DEFAULT 0;

    	");



	    $rows = $this->getDb()->query('select')->table('case_op_report_fields_template')
	    ->fields('id')
	    ->where('custom_field_id', 'IS NOT NULL', $this->getDb()->expr(''))
	    ->where('field', 'custom')
	    ->execute();

	    $this->getDb()->begin_transaction();
	    try {
		    foreach ($rows as $row) {
			    $this->getDb()->query('update')->table('case_op_report_fields_template')
				    ->data([
					    'is_custom' => 1,
				    ])
				    ->where('id', $row->id)
				    ->execute();
		    }
		    $this->getDb()->commit();
	    } catch (\Exception $e) {
		    $this->getDb()->rollback();
		    throw $e;
	    }


	$rows = $this->getDb()->query('select')->table('case_op_report_future_fields_template')
	    ->fields('id')
	    ->where('custom_field_id', 'IS NOT NULL', $this->getDb()->expr(''))
	    ->where('field', 'custom')
	    ->execute();

	$this->getDb()->begin_transaction();
	try {
	    foreach ($rows as $row) {
		    $this->getDb()->query('update')->table('case_op_report_future_fields_template')
			    ->data([
				    'is_custom' => 1,
			    ])
			    ->where('id', $row->id)
			    ->execute();
	    }
	    $this->getDb()->commit();
	} catch (\Exception $e) {
	    $this->getDb()->rollback();
	    throw $e;
	}


	$rows = $this->getDb()->query('select')->table('case_op_report_site_template')
	    ->fields('id')
	    ->where('custom_field_id', 'IS NOT NULL', $this->getDb()->expr(''))
	    ->where('field', 'custom')
	    ->execute();

	$this->getDb()->begin_transaction();
	try {
		foreach ($rows as $row) {
			$this->getDb()->query('update')->table('case_op_report_site_template')
				->data([
					'is_custom' => 1,
				])
				->where('id', $row->id)
				->execute();
		}
		$this->getDb()->commit();
	} catch (\Exception $e) {
		$this->getDb()->rollback();
		throw $e;
	}



	$rows = $this->getDb()->query('select')->table('case_op_report_custom_field_value')
	    ->fields('id', 'custom_field_id')
	    ->execute();
	$this->getDb()->begin_transaction();
	try {
	    foreach ($rows as $row) {
		    $customFields = $this->getDb()->query('select')->table('case_op_report_custom_field')
			    ->fields('id', 'name')
			    ->where('id', $row->custom_field_id)
			    ->execute()->as_array();

		    if($customFields) {
			    $this->getDb()->query('update')->table('case_op_report_custom_field_value')
				    ->data([
					    'name' => $customFields[0]->name,
				    ])
				    ->where('id', $row->id)
				    ->execute();
		    }
	    }
	    $this->getDb()->commit();
	} catch (\Exception $e) {
	    $this->getDb()->rollback();
	    throw $e;
	}


	$rows = $this->getDb()->query('select')->table('case_op_report_future_custom_field_value')
	    ->fields('id', 'custom_field_id')
	    ->execute();
	$this->getDb()->begin_transaction();
	try {
	    foreach ($rows as $row) {
		    $customFields = $this->getDb()->query('select')->table('case_op_report_custom_field')
			    ->fields('id', 'name')
			    ->where('id', $row->custom_field_id)
			    ->execute()->as_array();

		    if($customFields) {
			    $this->getDb()->query('update')->table('case_op_report_future_custom_field_value')
				    ->data([
					    'name' => $customFields[0]->name,
				    ])
				    ->where('id', $row->id)
				    ->execute();
		    }
	    }
	    $this->getDb()->commit();
	} catch (\Exception $e) {
	    $this->getDb()->rollback();
	    throw $e;
	}



	    $this->query("
	    	DROP TABLE `case_op_report_field_group`;
	    	DROP TABLE `case_op_report_custom_field`;
		ALTER TABLE `case_op_report_future_custom_field_value` DROP `custom_field_id`
		ALTER TABLE `case_op_report_custom_field_value` DROP `custom_field_id`
    		ALTER TABLE `case_op_report_fields_template` DROP `custom_field_id`;
    		ALTER TABLE `case_op_report_future_fields_template` DROP `custom_field_id`;
    		ALTER TABLE `case_op_report_site_template` DROP `custom_field_id`;
    		ALTER TABLE `case_op_report_fields_template` DROP `type`;
    		ALTER TABLE `case_op_report_future_fields_template` DROP `type`;
    		ALTER TABLE `case_op_report_site_template` DROP `type`;
    		ALTER TABLE `case_op_report_fields_template` DROP `show`;
    		ALTER TABLE `case_op_report_future_fields_template` DROP `show`;
    		ALTER TABLE `case_op_report_site_template` DROP `show`;
    		ALTER TABLE `case_op_report_fields_template` DROP `organization_id`;
    		ALTER TABLE `case_op_report_future_fields_template` DROP `organization_id`;

	");
    }
}
