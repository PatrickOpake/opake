<?php

use \Console\Migration\BaseMigration;
use Opake\Model\Analytics\UserActivity\ActivityRecord;

class OpReportsAction extends BaseMigration
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
			    'id' => ActivityRecord::ACTION_OP_REPORT_SIGN,
			    'name' => 'Sign Operative Report',
			    'zone' => 4
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => ActivityRecord::ACTION_OP_REPORT_AMENDED,
			    'name' => 'Amended Operative Report',
			    'zone' => 4
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => ActivityRecord::ACTION_OP_REPORT_SUBMITTED,
			    'name' => 'Submitted Operative Report',
			    'zone' => 4
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => ActivityRecord::ACTION_OP_REPORT_BEGIN,
			    'name' => 'Begin Operative Report',
			    'zone' => 4
		    ])->execute();
    }
}
