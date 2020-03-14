<?php

use \Console\Migration\BaseMigration;

class BookingSheetSnapshot extends BaseMigration
{
    public function change()
    {
        $this->query("
    		ALTER TABLE `case_chart` ADD `is_booking_sheet` TINYINT(1) NULL DEFAULT 0;	
    	");
    }
}
