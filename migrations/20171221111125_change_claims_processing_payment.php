<?php

use \Console\Migration\BaseMigration;

class ChangeClaimsProcessingPayment extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `billing_navicure_payment` CHANGE `total_payment` `patient_responsible_amount` decimal(12,2) DEFAULT NULL;
		");
    }
}
