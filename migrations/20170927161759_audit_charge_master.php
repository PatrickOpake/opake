<?php

use \Console\Migration\BaseMigration;

class AuditChargeMaster extends BaseMigration
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
	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_MASTER_CHARGE_DOWNLOAD,
			    'name' => 'Download Charge Master',
			    'zone' => 8
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_MASTER_CHARGE_UPLOAD,
			    'name' => 'Upload Charge Master',
			    'zone' => 8
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_MASTER_CHARGE_SAVE_EDITED,
			    'name' => 'Save Edited Charge Master',
			    'zone' => 8
		    ])->execute();
    }
}
