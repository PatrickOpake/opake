<?php

use \Console\Migration\BaseMigration;

class ChargeMasterColumns extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `master_charge` ADD `last_update` TINYINT(4)  NOT NULL  DEFAULT '0';
			ALTER TABLE `master_charge` ADD `archived` TINYINT(4)  NOT NULL  DEFAULT '0'  AFTER `last_update`;
		");
    }
}
