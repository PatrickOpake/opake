<?php

use \Console\Migration\BaseMigration;

class AddNewFieldToOpReport extends BaseMigration
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
    		ALTER TABLE `case_op_report` ADD COLUMN `scribe` MEDIUMTEXT NULL DEFAULT NULL;
    		ALTER TABLE `case_op_report_future` ADD COLUMN `scribe` MEDIUMTEXT NULL DEFAULT NULL;
    	");

    	$fieldName = 'scribe';

	    $siteTemplate = $this->getDb()->query('select')->table('case_op_report_site_template')
		    ->group_by('organization_id')
		    ->execute()->as_array();

	    if (!empty($siteTemplate)) {
		    foreach ($siteTemplate as $item) {
			    if(isset($item->organization_id)) {
				    $this->getDb()->query('insert')
					    ->table('case_op_report_site_template')
					    ->data([
						    'organization_id' => $item->organization_id,
						    'field' => $fieldName,
						    'active' => 0,
						    'group_id' => \Opake\Model\Cases\OperativeReport\SiteTemplate::getGroupIdByField($fieldName),
						    'sort' => \Opake\Model\Cases\OperativeReport\SiteTemplate::getSortByField($fieldName)
					    ])->execute();
			    }
		    }

	    }

	    $futureTemplate = $this->getDb()->query('select')->table('case_op_report_future_fields_template')
		    ->group_by('future_template_id')
		    ->execute()->as_array();

	    if(!empty($futureTemplate)) {
		    foreach ($futureTemplate as $item) {
		    	$this->getDb()->query('insert')
				    ->table('case_op_report_future_fields_template')
				    ->data([
					    'future_template_id' => $item->future_template_id,
					    'field' => $fieldName,
					    'active' => 0,
					    'group_id' => \Opake\Model\Cases\OperativeReport\SiteTemplate::getGroupIdByField($fieldName),
					    'sort' => \Opake\Model\Cases\OperativeReport\SiteTemplate::getSortByField($fieldName)
				    ])->execute();
		    }
	    }

	    $reportTemplate = $this->getDb()->query('select')->table('case_op_report_fields_template')
		    ->group_by('report_id')
		    ->execute()->as_array();

	    if(!empty($reportTemplate)) {
		    foreach ($reportTemplate as $item) {
			    $this->getDb()->query('insert')
				    ->table('case_op_report_fields_template')
				    ->data([
					    'report_id' => $item->report_id,
					    'field' => $fieldName,
					    'active' => 0,
					    'group_id' => \Opake\Model\Cases\OperativeReport\SiteTemplate::getGroupIdByField($fieldName),
					    'sort' => \Opake\Model\Cases\OperativeReport\SiteTemplate::getSortByField($fieldName)
				    ])->execute();
		    }
	    }
    }
}
