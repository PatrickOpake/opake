<?php

use \Console\Migration\BaseMigration;

class AddNewFlagsInCaseAndBooking extends BaseMigration
{
    public function change()
    {
        $this->query("
    		ALTER TABLE `case` ADD `special_equipment_flag` TINYINT(4) DEFAULT NULL AFTER `special_equipment_implants`,
    		    ADD `implants_flag` TINYINT(4) DEFAULT NULL AFTER `implants`;
    		
    		ALTER TABLE `booking_sheet` ADD `special_equipment_flag` TINYINT(4) DEFAULT NULL AFTER `special_equipment_implants`,
    		    ADD `implants_flag` TINYINT(4) DEFAULT NULL AFTER `implants`;
    	");
    }
}
