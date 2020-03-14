<?php

use \Console\Migration\BaseMigration;

class LedgerRespAmounts extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `billing_ledger_applied_payment` ADD `resp_co_pay_amount` DECIMAL(12,2)  NULL  DEFAULT NULL  AFTER `amount`;
			ALTER TABLE `billing_ledger_applied_payment` ADD `resp_co_ins_amount` DECIMAL(12,2)  NULL  DEFAULT NULL  AFTER `resp_co_pay_amount`;
			ALTER TABLE `billing_ledger_applied_payment` ADD `resp_deduct_amount` DECIMAL(12,2)  NULL  DEFAULT NULL  AFTER `resp_co_ins_amount`;
		");
    }
}
