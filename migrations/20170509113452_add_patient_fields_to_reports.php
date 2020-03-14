<?php

use \Console\Migration\BaseMigration;

class AddPatientFieldsToReports extends BaseMigration
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
		    $reportTemplate = $this->getDb()->query('select')->table('case_op_report_fields_template')
			    ->where('field', '!=', $field)
			    ->group_by('report_id')
			    ->execute()->as_array();

		    $futureTemplate = $this->getDb()->query('select')->table('case_op_report_future_fields_template')
			    ->group_by('future_template_id')
			    ->execute()->as_array();

		    foreach ($reportTemplate as $item) {
			    $IsExistReportTemplate = $this->getDb()->query('select')->table('case_op_report_fields_template')
				    ->where('field', $field)
				    ->where('id', $item->id)
				    ->execute()->as_array();

			    if(empty($IsExistReportTemplate)) {
				    if(isset($item->report_id)) {
					    $this->getDb()->query('insert')
						    ->table('case_op_report_fields_template')
						    ->data([
							    'report_id' => $item->report_id,
							    'field' => $field,
							    'active' => 1,
							    'group_id' => \Opake\Model\Cases\OperativeReport\SiteTemplate::getGroupIdByField($field),
							    'sort' => \Opake\Model\Cases\OperativeReport\SiteTemplate::getSortByField($field)
						    ])->execute();
				    }
			    }
		    }

		    foreach ($futureTemplate as $item) {
			    $IsExist = $this->getDb()->query('select')->table('case_op_report_future_fields_template')
				    ->where('field', $field)
				    ->where('id', $item->id)
				    ->execute()->as_array();

			    if(empty($IsExist)) {
				    if(isset($item->future_template_id)) {
					    $this->getDb()->query('insert')
						    ->table('case_op_report_future_fields_template')
						    ->data([
							    'future_template_id' => $item->future_template_id,
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
