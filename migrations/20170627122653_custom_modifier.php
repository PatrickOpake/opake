<?php

use \Console\Migration\BaseMigration;

class CustomModifier extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `case_coding_bill` ADD `custom_modifier` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `sort`;
		");
    }
}
