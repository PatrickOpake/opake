<?php

use \Console\Migration\BaseMigration;

class BookingActionsActivity extends BaseMigration
{
    public function change()
    {
	    try {
		    $this->getDb()->query('insert')
			    ->table('user_activity_action_zone')
			    ->data([
				    'id' => 11,
				    'name' => 'Charts'
			    ])->execute();
	    } catch (\Exception $e) {
		    print $e->getMessage() . "\r\n";
	    }

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BOOKING_ADD_NOTE,
			    'name' => 'Add Note',
			    'zone' => 10
		    ])->execute();
    }
}
