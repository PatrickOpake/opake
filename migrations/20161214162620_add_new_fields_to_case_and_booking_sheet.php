<?php

use \Console\Migration\BaseMigration;

class AddNewFieldsToCaseAndBookingSheet extends BaseMigration
{
    public function change()
    {
        $this->query("
			ALTER TABLE `booking_sheet` ADD `date_of_injury` DATE NULL DEFAULT NULL AFTER `patients_relations`,
			    ADD `is_unable_to_work` TINYINT(4) NULL DEFAULT NULL AFTER `patients_relations`,
			    ADD `unable_to_work_from` DATE NULL DEFAULT NULL AFTER `patients_relations`,
			    ADD `unable_to_work_to` DATE NULL DEFAULT NULL AFTER `patients_relations`;
			    
            ALTER TABLE `case` ADD `date_of_injury` DATE NULL DEFAULT NULL,
			    ADD `is_unable_to_work` TINYINT(4) NULL DEFAULT NULL,
			    ADD `unable_to_work_from` DATE NULL DEFAULT NULL,
			    ADD `unable_to_work_to` DATE NULL DEFAULT NULL;
		");
    }
}
