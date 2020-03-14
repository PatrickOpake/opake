<?php

use \Console\Migration\BaseMigration;

class IsSelfFunded extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `insurance_data_regular` ADD `is_self_funded` TINYINT(1)  NULL  DEFAULT '0'  AFTER `insurance_state_id`;
		");
    }
}
