<?php

use \Console\Migration\BaseMigration;

class UpdateReportLogic extends BaseMigration
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
		    ->table('case_op_report')
		    ->where('surgeon_id', 'IS NULL', $this->getDb()->expr(''))
		    ->execute();

		    foreach ($rows as $row) {
			    $caseUsers = $this->getDb()->query('select')
				    ->table('case_user')
				    ->where('case_id', $row->case_id)
				    ->execute()->as_array();

			    if($caseUsers) {
				$surgeon_id = $caseUsers[0]->user_id;
				$this->getDb()->query('update')
				    ->table('case_op_report')
				    ->data(['surgeon_id' => $surgeon_id])
				    ->where('id', $row->id)
				    ->execute();
			    }
		    }
    }
}
