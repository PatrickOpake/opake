<?php

use \Console\Migration\BaseMigration;

class BookingScheduleActivity extends BaseMigration
{
    public function change()
    {
	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BOOKING_SCHEDULE,
			    'name' => 'Schedule Booking',
			    'zone' => 10
		    ])->execute();
    }
}
