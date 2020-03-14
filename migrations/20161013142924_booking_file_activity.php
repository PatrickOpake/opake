<?php

use \Console\Migration\BaseMigration;

class BookingFileActivity extends BaseMigration
{
    public function change()
    {
	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BOOKING_FILE_UPLOAD,
			    'name' => 'Upload File',
			    'zone' => 10
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BOOKING_FILE_RENAME,
			    'name' => 'Rename File',
			    'zone' => 10
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BOOKING_FILE_REMOVE,
			    'name' => 'Remove File',
			    'zone' => 10
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BOOKING_PRINT,
			    'name' => 'Print',
			    'zone' => 10
		    ])->execute();
    }
}
