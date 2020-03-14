<?php

use \Console\Migration\BaseMigration;

class ApiOpReports extends BaseMigration
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
    			ALTER TABLE `case_op_report` ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT '0';
    			ALTER TABLE `case_op_report` ADD COLUMN `is_exist_template` TINYINT(1) NOT NULL DEFAULT '0';
    		");
	    } catch (\Exception $e) {
		    $this->writeln("can't add is_active && is_exist_template");
	    }

	    $db = $this->getDb();
	    $db->begin_transaction();
	    try {
		    $rows = $this->getDb()->query('select')->table('case_op_report')
			    ->join('case', ['case.id', 'case_op_report.case_id'])
			    ->fields('id', 'type', $this->getDb()->expr('case.organization_id as org_id'))
			    ->execute();

		    foreach ($rows as $row) {
			    $reportTemplates = $this->getDb()->query('select')->table('case_op_report_fields_template')
				    ->where('report_id', $row->id)
				    ->where('field', 'IN', $this->getDb()->arr(\Opake\Model\Cases\OperativeReport::getTypeSurgeons()))
				    ->execute()->as_array();

			    if($reportTemplates) {
				    foreach ($reportTemplates as $template) {
					    if($template->field === $row->type) {
						    $this->getDb()->query('update')->table('case_op_report')
							    ->data([
								    'is_exist_template' => 1,
								    'is_active' => $template->active,
							    ])
							    ->where('id', $row->id)
							    ->execute();
					    }
				    }
			    } else {
				    $siteTemplate = $this->getDb()->query('select')->table('case_op_report_site_template')
					    ->where('organization_id', $row->org_id)
					    ->where('field', 'IN', $this->getDb()->arr(\Opake\Model\Cases\OperativeReport::getTypeSurgeons()))
					    ->execute()->as_array();
				    if($siteTemplate) {
					    foreach ($siteTemplate as $template) {
						    if($template->field === $row->type) {
							    $this->getDb()->query('update')->table('case_op_report')
								    ->data([
									    'is_active' => $template->active,
								    ])
								    ->where('id', $row->id)
								    ->execute();
						    }
					    }
				    } else {
					    $fieldsActivity =  \Opake\Model\Cases\OperativeReport\SiteTemplate::getFields();
				    	if(isset($fieldsActivity[$row->type])) {
						$this->getDb()->query('update')->table('case_op_report')
							->data([
								'is_active' => $fieldsActivity[$row->type],
							])
							->where('id', $row->id)
							->execute();

					}
				    }
			    }
		    }
		    $db->commit();

	    } catch (\Exception $e) {
		    $db->rollback();
		    $this->writeln("can't update case_op_report set activity");
		    throw $e;
	    }

    }
}
