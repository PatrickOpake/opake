<?php

use \Console\Migration\BaseMigration;

class LedgerAmountFloat extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `billing_ledger_payment_activity` CHANGE `amount` `amount` FLOAT(11)  NULL  DEFAULT NULL;
		");
    }
}
