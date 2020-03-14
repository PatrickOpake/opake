<?php

use \Console\Migration\BaseMigration;

class AllowPracticeGroupsToScheduleBlocks extends BaseMigration
{
    public function change()
    {
        $this->query("
    		ALTER TABLE `case_blocking` ADD `practice_id` INT(11) NULL DEFAULT NULL AFTER `doctor_id`,
    		    CHANGE `doctor_id` `doctor_id` INT(11) NULL DEFAULT NULL;

            ALTER TABLE `case_blocking_item` ADD `practice_id` INT(11) NULL DEFAULT NULL AFTER `doctor_id`,
    		    CHANGE `doctor_id` `doctor_id` INT(11) NULL DEFAULT NULL;
    	");
    }
}
