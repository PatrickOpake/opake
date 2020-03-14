<?php

use \Console\Migration\BaseMigration;

class CodingRevenueCode extends BaseMigration
{
	public function change()
	{
		$this->query("
			ALTER TABLE `case_coding_bill` ADD `revenue_code` VARCHAR(50) NULL DEFAULT NULL  AFTER `quantity`;
		");
	}
}