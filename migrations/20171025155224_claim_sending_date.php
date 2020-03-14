<?php

use \Console\Migration\BaseMigration;

class ClaimSendingDate extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `billing_navicure_claim`
				ADD `sending_date` DATETIME  NULL  DEFAULT NULL  AFTER `last_transaction_date`;
		");
    }
}
