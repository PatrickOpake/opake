<?php

use \Console\Migration\BaseMigration;

class UpdatePermissionsOpReport extends BaseMigration
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
    		ALTER TABLE `user` CHANGE COLUMN `is_hide_operative_report` `is_enabled_op_report` TINYINT NULL DEFAULT '0';
    	");


	$rows = $this->getDb()->query('select')
	    ->table('user')
	    ->execute();

	    $this->getDb()->begin_transaction();

	    try {
		    foreach ($rows as $row) {
			    $this->getDb()->query('update')
				    ->table('user')
				    ->data(['is_enabled_op_report' => !$row->is_enabled_op_report])
				    ->where('id', $row->id)
				    ->execute();
		    }
		    $this->getDb()->commit();
	    } catch (\Exception $e) {
		    $this->getDb()->rollback();
		    throw $e;
	    }

    }
}
