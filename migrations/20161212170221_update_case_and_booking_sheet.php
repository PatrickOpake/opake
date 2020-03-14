<?php

use \Console\Migration\BaseMigration;

class UpdateCaseAndBookingSheet extends BaseMigration
{
    public function change()
    {
        $this->query("
			ALTER TABLE `booking_sheet` ADD `referring_provider_name` VARCHAR(255) NULL DEFAULT NULL AFTER `point_of_origin`,
			    ADD `referring_provider_npi` VARCHAR(255) NULL DEFAULT NULL AFTER `point_of_origin`,
			    ADD `prior_auth_number` INT(11) NULL DEFAULT NULL AFTER `point_of_origin`;
			    
            ALTER TABLE `case` ADD `referring_provider_name` VARCHAR(255) NULL DEFAULT NULL,
			    ADD `referring_provider_npi` VARCHAR(255) NULL DEFAULT NULL,
			    ADD `prior_auth_number` INT(11) NULL DEFAULT NULL;
		");
    }
}
