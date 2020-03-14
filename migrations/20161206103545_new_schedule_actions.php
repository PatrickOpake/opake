<?php

use \Console\Migration\BaseMigration;

class NewScheduleActions extends BaseMigration
{
    public function change()
    {
	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_RESCHEDULE_CASE,
			    'name' => 'Reschedule Case',
			    'zone' => 2
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_DELETE_CASE,
			    'name' => 'Delete Case',
			    'zone' => 2
		    ])->execute();
    }
}
