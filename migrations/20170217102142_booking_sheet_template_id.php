<?php

use \Console\Migration\BaseMigration;

class BookingSheetTemplateId extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `booking_sheet`
				ADD `template_id` INT(11) NULL DEFAULT NULL
				AFTER `booking_patient_id`;
		");
    }
}
