<?php

use \Console\Migration\BaseMigration;

class RemoveDuplicatesFromTemplates extends BaseMigration
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
	    $rows = $this->getDb()->query('select')
		    ->table('case_op_report_future_fields_template')
		    ->group_by('future_template_id')
		    ->execute();

	    foreach ($rows as $row) {
		    $r = $this->getDb()->query('select')
			    ->fields('id', 'future_template_id', 'field')
			    ->table('case_op_report_future_fields_template')
			    ->where('future_template_id', $row->future_template_id)
			    ->where('group_id', 1)
			    ->group_by('field')
			    ->having($this->getDb()->expr('COUNT(*)'), '>', 1)
			    ->execute();

		    foreach ($r as $item) {
		    	$this->getDb()->query('delete')
				->table('case_op_report_future_fields_template')
				->where('id', $item->id)
				->execute();
		    }
	    }


	    $rows = $this->getDb()->query('select')
		    ->table('case_op_report_fields_template')
		    ->group_by('report_id')
		    ->execute();

	    foreach ($rows as $row) {
		    $r = $this->getDb()->query('select')
			    ->fields('id', 'report_id', 'field')
			    ->table('case_op_report_fields_template')
			    ->where('report_id', $row->report_id)
			    ->where('group_id', 1)
			    ->group_by('field')
			    ->having($this->getDb()->expr('COUNT(*)'), '>', 1)
			    ->execute();

		    foreach ($r as $item) {
			    $this->getDb()->query('delete')
				    ->table('case_op_report_fields_template')
				    ->where('id', $item->id)
				    ->execute();
		    }


	    }

    }
}
