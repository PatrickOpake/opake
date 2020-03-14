<?php

use \Console\Migration\BaseMigration;

class ChargeMasterDecimal extends BaseMigration
{
    public function change()
    {
		$this->query('
			ALTER TABLE `master_charge` CHANGE `amount` `amount` DECIMAL(12,2)  NULL  DEFAULT NULL;
			ALTER TABLE `master_charge` CHANGE `unit_price` `unit_price` DECIMAL(12,2)  NULL  DEFAULT NULL;
		');
    }
}
