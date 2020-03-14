<?php

use \Console\Migration\BaseMigration;

class BookingCreatePatientActivity extends BaseMigration
{
    public function change()
    {
        $this->getDb()->query('insert')
            ->table('user_activity_action')
            ->data([
                'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BOOKING_PATIENT_CREATE,
                'name' => 'Create Patient',
                'zone' => 10
            ])->execute();
    }
}
