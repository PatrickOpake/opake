<?php

use \Console\Migration\BaseMigration;

class CodingQuantity extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `case_coding_bill` ADD `quantity` INT(11) NULL DEFAULT NULL  AFTER `case_type_id`;
		");
    }
}