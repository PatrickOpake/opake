<?php

use \Console\Migration\BaseMigration;

class AuditFields extends BaseMigration
{
    public function change()
    {
	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_SETTINGS_EDIT_SMS_TEMPLATE,
			    'name' => 'Edit SMS Template',
			    'zone' => 8
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_SEND_POINT_OF_CONTACT_SMS,
			    'name' => 'Send Point of Contact SMS',
			    'zone' => 2
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CASE_CHECK_IN,
			    'name' => 'Case Check In',
			    'zone' => 2
		    ])->execute();
    }
}
