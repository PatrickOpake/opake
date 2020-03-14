<?php

use \Console\Migration\BaseMigration;

class FeeScheduleMods extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `billing_fee_schedule` CHANGE `mod` `mod` VARCHAR(1024) NULL DEFAULT NULL;
		");
    }
}
