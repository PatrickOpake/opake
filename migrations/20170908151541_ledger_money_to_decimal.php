<?php

use \Console\Migration\BaseMigration;

class LedgerMoneyToDecimal extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `billing_ledger_applied_payment` CHANGE `amount` `amount` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_ledger_payment_info` CHANGE `total_amount` `total_amount` DECIMAL(12,2)  NULL  DEFAULT NULL;
		");

	    $this->query("
	        ALTER TABLE `billing_eob` CHANGE `charge_master_amount` `charge_master_amount` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `billing_eob` CHANGE `amount_reimbursed` `amount_reimbursed` DECIMAL(12,2)  NULL  DEFAULT NULL;
	    ");
    }
}
