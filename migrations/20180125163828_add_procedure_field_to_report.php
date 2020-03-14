<?php

use \Console\Migration\BaseMigration;

class AddProcedureFieldToReport extends BaseMigration
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
	    $db = $this->getDb();
	    $db->begin_transaction();

	    $field = 'procedure';

	    try {
		    $reportTemplate = $this->getDb()->query('select')->table('case_op_report_fields_template')
			    ->where('field', '!=', $field)
			    ->group_by('report_id')
			    ->execute()->as_array();

		    foreach ($reportTemplate as $item) {

			    $IsExistReportTemplate = $this->getDb()->query('select')->table('case_op_report_fields_template')
				    ->where('field', $field)
				    ->where('report_id', $item->report_id)
				    ->execute()->as_array();

			    if (empty($IsExistReportTemplate)) {
				    if (isset($item->report_id)) {
					    $this->getDb()->query('insert')
						    ->table('case_op_report_fields_template')
						    ->data([
							    'organization_id' => $item->organization_id,
							    'report_id' => $item->report_id,
							    'field' => $field,
							    'active' => 1,
							    'name' => \Opake\Model\Cases\OperativeReport\SiteTemplate::getNameByField($field),
							    'group_id' => \Opake\Model\Cases\OperativeReport\SiteTemplate::GROUP_DESCRIPTIONS_ID,
							    'sort' => 1,
							    'type' => \Opake\Model\Cases\OperativeReport\SiteTemplate::FIELD_TYPE_CASE_TYPE
						    ])->execute();
				    }
			    }
		    }


		    $db->commit();
	    } catch (\Exception $e) {
		    $db->rollback();
		    $this->writeln("can't update case_op_report_fields_template");
		    throw $e;
	    }
    }
}
