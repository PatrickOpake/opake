<?php

use \Console\Migration\BaseMigration;

class CheckNumberAndClaimId extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `billing_ledger_payment_activity`
			ADD `check_number` VARCHAR(40)  NULL  DEFAULT NULL;
		");
	    $this->query("
			ALTER TABLE `billing_ledger_payment_activity`
			ADD `claim_id` VARCHAR(40)  NULL  DEFAULT NULL;
		");
	    $this->query("
	       ALTER TABLE `billing_ledger_payment_activity`
	       ADD `insurance_payer_id` INT  NULL  DEFAULT NULL;
	    ");
    }
}
