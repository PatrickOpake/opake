<?php

use \Console\Migration\BaseMigration;

class DropLedgerPaymentActivityTable extends BaseMigration
{
    public function change()
    {
		$this->query("
			DROP TABLE `billing_ledger_payment_activity`;
		");
    }
}
