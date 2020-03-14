<?php

use \Console\Migration\BaseMigration;

class PaymentPostingLedgerChanges extends BaseMigration
{
    public function change()
    {
	    $this->query("
	        ALTER TABLE `billing_payment_posting_applied_payment` ADD `ledger_activity_id` INT UNSIGNED NULL DEFAULT NULL AFTER `patient_payment_id`;
	       	ALTER TABLE `billing_ledger_payment_activity` DROP `notes`;
	    ");
    }
}
