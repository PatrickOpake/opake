<?php

use \Console\Migration\BaseMigration;

class AlterCaseReportTable extends BaseMigration
{
    public function change()
    {
        $this->query("
	        ALTER TABLE `billing_cases_report`
	            ADD COLUMN `account_number` VARCHAR(255) NULL DEFAULT NULL AFTER `organization_id`,
	            CHANGE COLUMN `notes` `notes` VARCHAR(500) NULL DEFAULT NULL;
        ");
    }
}
