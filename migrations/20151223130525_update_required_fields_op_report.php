<?php

use \Console\Migration\BaseMigration;

class UpdateRequiredFieldsOpReport extends BaseMigration
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
		$required_fields = ['dob', 'mrn', 'admit_type', 'room', 'post_op_diagnosis', 'specimens_removed', 'ebl', 'clinical_history', 'findings', 'description_procedure'];

		$this->getDb()->query('update')->table('case_info_template')
			->data([
				'active' => 1
			])
			->where('case_info_template.field', 'IN', $this->getDb()->expr('("' . implode('","', $required_fields) . '")'))
			->execute();

		$this->getDb()->query('update')->table('case_op_report_template')
			->data([
				'active' => 1
			])
			->where('case_op_report_template.field', 'IN', $this->getDb()->expr('("' . implode('","', $required_fields) . '")'))
			->execute();
	}
}
