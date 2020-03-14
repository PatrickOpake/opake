<?php

use \Console\Migration\BaseMigration;

class AddPatientFieldsToOpReportTemplate extends BaseMigration
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
		$fields = [
			'patient_name',
			'age_sex',
			'dob',
			'mrn',
			'room',
			'dos',
			'acc_number',
			'time_scheduled',
		];

		foreach ($fields as $field) {
			$siteTemplate = $this->getDb()->query('select')->table('case_op_report_site_template')
				->where('field', '!=', $field)
				->group_by('organization_id')
				->execute()->as_array();

			if (!empty($siteTemplate)) {
				foreach ($siteTemplate as $item) {
					if(isset($item->organization_id)) {
						$this->getDb()->query('insert')
							->table('case_op_report_site_template')
							->data([
								'organization_id' => $item->organization_id,
								'field' => $field,
								'active' => 1,
								'group_id' => \Opake\Model\Cases\OperativeReport\SiteTemplate::getGroupIdByField($field),
								'sort' => \Opake\Model\Cases\OperativeReport\SiteTemplate::getSortByField($field)
							])->execute();
					}
				}

			}
		}
	}
}
