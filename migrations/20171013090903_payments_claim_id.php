<?php

use \Console\Migration\BaseMigration;

class PaymentsClaimId extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `billing_ledger_applied_payment` ADD `claim_id` INT(11) UNSIGNED  NULL  DEFAULT NULL  AFTER `resp_deduct_amount`;
		");
    }
}
