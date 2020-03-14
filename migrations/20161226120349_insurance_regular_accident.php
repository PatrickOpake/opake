<?php

use \Console\Migration\BaseMigration;

class InsuranceRegularAccident extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `insurance_data_regular`
				ADD COLUMN `is_accident` TINYINT(1) NULL DEFAULT 0;
		");
    }
}