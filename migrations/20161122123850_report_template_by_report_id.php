<?php

use \Console\Migration\BaseMigration;

class ReportTemplateByReportId extends BaseMigration
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
		$caseReports = [];
		$this->getDb()->begin_transaction();
		$rows = $this->getDb()->query('select')->table('case_op_report_fields_template')
		    ->fields('id', 'case_id')
		    ->group_by('case_id')
		    ->execute();
	    try {

		    foreach ($rows as $row) {
			    $users = $this->getDb()->query('select')->table('user')
				    ->fields('id')
				    ->join('case_user', ['user.id', 'case_user.user_id'], 'inner')
				    ->where('case_user.case_id', $row->case_id)
				    ->order_by('case_user.order')
				    ->execute()->as_array();
			    if($users) {
				    $surgeon_id = $users[0]->id;

				    $report = $this->getDb()->query('select')->table('case_op_report')
					    ->fields('id')
					    ->where('case_id', $row->case_id)
					    ->where('type', \Opake\Model\Cases\OperativeReport::TYPE_SURGEON)
					    ->where('surgeon_id', $surgeon_id)
					    ->execute()->as_array();

				    if($report) {
					    $caseReports[$row->case_id] = $report[0]->id;
				    }
			    }
		    }

		    $rows = $this->getDb()->query('select')->table('case_op_report_fields_template')
			    ->fields('id', 'case_id')
			    ->execute();

		    foreach ($rows as $row) {
		    	if(isset($caseReports[$row->case_id])) {
				$this->getDb()->query('update')
					->table('case_op_report_fields_template')
					->data([
						'case_id' => $caseReports[$row->case_id]
					])
					->where('id', $row->id)
					->execute();
			} else {
				$this->getDb()->query('delete')
					->table('case_op_report_fields_template')
					->where('case_id', $row->case_id)
					->execute();
			}

		    }

		    $this->getDb()->commit();

	    } catch (\Exception $e) {
		    $this->getDb()->rollback();
		    throw $e;
	    }

    	$this->query("
    		ALTER TABLE `case_op_report_fields_template` CHANGE `case_id` `report_id` INT(11) NULL DEFAULT NULL;
    	");


    }
}
