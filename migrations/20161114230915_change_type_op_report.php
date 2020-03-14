<?php

use \Console\Migration\BaseMigration;

class ChangeTypeOpReport extends BaseMigration
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
    	$types = [
    		0 => 'surgeon',
    		1 => 'anesthesiologist',
    		2 => 'co_surgeon',
    		3 => 'supervising_surgeon',
    		4 => 'first_assistant_surgeon',
    		5 => 'assistant',
    		7 => 'dictated_by',
    		8 => 'other_staff',
    		9 => 'non_surgeon',
	];

    	$this->query("
    		ALTER TABLE `case_op_report` CHANGE `type` `type` VARCHAR(255) NULL DEFAULT NULL;
    	");

	    $rows = $this->getDb()->query('select')
		    ->table('case_op_report')
		    ->execute();

	    foreach ($rows as $row) {
		    $this->getDb()->query('update')
			    ->table('case_op_report')
			    ->data(['type' => $types[$row->type]])
			    ->where('id', $row->id)
			    ->execute();
	    }
    }
}
