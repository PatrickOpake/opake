<?php

use \Console\Migration\BaseMigration;

class CodingBillingUpdates extends BaseMigration
{
    public function change()
    {
        $this->query("
	    ALTER TABLE `case_coding_bill` ADD INDEX `IDX_coding_id` (`coding_id`);
	    ALTER TABLE `case_coding_bill` ADD `diagnosis_row` INT UNSIGNED NULL AFTER `fee_id`;
        ");
    }
}
