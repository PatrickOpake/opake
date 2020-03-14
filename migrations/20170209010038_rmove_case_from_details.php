<?php

use \Console\Migration\BaseMigration;

class RmoveCaseFromDetails extends BaseMigration
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
		    ->table('user_activity')
		    ->execute();

	    $this->getDb()->begin_transaction();

	    try {

		    foreach ($rows as $row) {
			    if ($row->details) {
				    $data = unserialize($row->details);
				    if (isset($data['case']) || isset($data['case_id'])) {
				    	if(isset($data['case'])) {
						unset($data['case']);
					}
					if(isset($data['case_id'])) {
						unset($data['case_id']);
					}

					$this->getDb()->query('update')->table('user_activity')
					    ->data([
						    'details' => serialize($data)
					    ])
					    ->where('id', $row->id)
					    ->execute();
				    }
			    }
		    }

		    $this->getDb()->commit();

	    } catch (\Exception $e) {
		    $this->getDb()->rollback();
		    throw $e;

	    }
    }
}
