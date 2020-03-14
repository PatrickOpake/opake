<?php

use \Console\Migration\BaseMigration;

class AddImplantsFieldToCaseAndBooking extends BaseMigration
{
    public function change()
    {
        $this->query("
    		ALTER TABLE `case` ADD `implants` VARCHAR(255) DEFAULT NULL AFTER `special_equipment_implants`;
    		ALTER TABLE `booking_sheet` ADD `implants` VARCHAR(255) DEFAULT NULL AFTER `special_equipment_implants`;
    	");
    }
}
