<?php

use \Console\Migration\BaseMigration;

class FlaggingComments extends BaseMigration
{
    public function change()
    {
        $this->query("
			ALTER TABLE `case_note` ADD `patient_id` INT(11) NULL DEFAULT NULL AFTER `user_id`;
		");
    }
}
