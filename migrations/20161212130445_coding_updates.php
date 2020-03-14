<?php

use \Console\Migration\BaseMigration;

class CodingUpdates extends BaseMigration
{
    public function change()
    {
	    $this->query("
		    ALTER TABLE `case_coding` ADD `reference_number` VARCHAR(255) NULL DEFAULT NULL AFTER `bill_type`,
					ADD `has_lab_services_outside` tinyint(1) NULL DEFAULT NULL AFTER `reference_number`,
					ADD `lab_services_outside_amount` FLOAT NULL DEFAULT NULL AFTER `has_lab_services_outside`,
					ADD `amount_paid` FLOAT NULL DEFAULT NULL AFTER `lab_services_outside_amount`,
					ADD `addition_claim_information` TEXT DEFAULT NULL AFTER `amount_paid`;
	    ");
    }
}
