<?php

use \Console\Migration\BaseMigration;

class FlagCommentForBookingSheet extends BaseMigration
{
    public function change()
    {
        $this->query("
			ALTER TABLE `booking_note` ADD `patient_id` INT(11) NULL DEFAULT NULL AFTER `user_id`;
		");
    }
}
