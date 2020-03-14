<?php

use \Console\Migration\BaseMigration;

class AddOtherStaffToBooking extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `booking_other_staff` (
                  `staff_id` int(11) NOT NULL,
                  `booking_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            ALTER TABLE `booking_other_staff`
                ADD UNIQUE KEY `uni` (`staff_id`,`booking_id`);
        ");
    }
}
