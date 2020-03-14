<?php

use \Console\Migration\BaseMigration;

class CodingInsurancePayerId extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `case_coding_insurance` ADD `payer_id` INT(11)  NULL  DEFAULT NULL  AFTER `order_number`;
		");
    }
}