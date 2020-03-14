<?php

use \Console\Migration\BaseMigration;

class UpdateBillingNoteActivityName extends BaseMigration
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
	    $this->getDb()->query('update')
		    ->table('user_activity_action')
		    ->data([
			    'name' => 'Edit Note',
		    ])
		    ->where('id', \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BILLING_NOTES_EDITED)->execute();

	    $this->getDb()->query('update')
		    ->table('user_activity_action')
		    ->data([
			    'name' => 'Create Note',
		    ])
		    ->where('id', \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BILLING_NOTES_SAVED)->execute();

	    $this->getDb()->query('update')
		    ->table('user_activity_action')
		    ->data([
			    'name' => 'Delete Note',
		    ])
		    ->where('id', \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BILLING_NOTES_DELETED)->execute();



	    $this->getDb()->query('update')
		    ->table('user_activity_action')
		    ->data([
			    'name' => 'Edit Note',
		    ])
		    ->where('id', \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_NOTES_EDITED)->execute();

	    $this->getDb()->query('update')
		    ->table('user_activity_action')
		    ->data([
			    'name' => 'Create Note',
		    ])
		    ->where('id', \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_NOTES_SAVED)->execute();

	    $this->getDb()->query('update')
		    ->table('user_activity_action')
		    ->data([
			    'name' => 'Delete Note',
		    ])
		    ->where('id', \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_NOTES_DELETED)->execute();

    }
}
