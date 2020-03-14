<?php

use \Console\Migration\BaseMigration;

class LedgerRelatedParentPayment extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `billing_ledger_applied_payment` ADD `related_parent_payment_id` INT(11)  UNSIGNED  NULL  DEFAULT NULL  AFTER `claim_id`;
		");
    }
}
