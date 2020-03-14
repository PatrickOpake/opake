<?php

use \Console\Migration\BaseMigration;

class BookingActivity extends BaseMigration
{
    public function change()
    {
	    $this->getDb()->query('insert')
		    ->table('user_activity_action_zone')
		    ->data([
			    'id' => 10,
			    'name' => 'Booking'
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BOOKING_CREATE,
			    'name' => 'Create Booking',
			    'zone' => 10
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BOOKING_EDIT,
			    'name' => 'Edit Booking',
			    'zone' => 10
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BOOKING_REMOVE,
			    'name' => 'Remove Booking',
			    'zone' => 10
		    ])->execute();
    }
}
