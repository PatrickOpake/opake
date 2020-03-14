<?php

use \Console\Migration\BaseMigration;

class UpdateCaseBlockingTable extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `case_blocking` 
                ADD COLUMN `daily_every` TEXT NULL DEFAULT NULL,
                ADD COLUMN `monthly_every` TEXT NULL DEFAULT NULL,
                ADD COLUMN `day_number` TINYINT(4) NULL DEFAULT NULL,
                ADD COLUMN `month_number` TINYINT(4) NULL DEFAULT NULL,
                ADD COLUMN `monthly_day` TINYINT(4) NULL DEFAULT NULL,
                ADD COLUMN `monthly_week` TINYINT(4) NULL DEFAULT NULL;
        ");
	}
}
