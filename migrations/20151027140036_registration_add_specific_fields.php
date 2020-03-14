<?php

use \Console\Migration\BaseMigration;

class RegistrationAddSpecificFields extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `case_registration` ADD `accompanied` VARCHAR(255) NULL;
	    ALTER TABLE `case_registration` ADD `mobility` TINYINT NULL ;
        ");
	}
}
