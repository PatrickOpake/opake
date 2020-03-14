<?php

use \Console\Migration\BaseMigration;

class NonCoveredCharges extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `billing_fee_schedule` ADD `non_covered_charges` float DEFAULT NULL;
		");
    }
}
