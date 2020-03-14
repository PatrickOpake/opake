<?php

use \Console\Migration\BaseMigration;

class RegistrationDobToDatetime extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `case_registration`
                CHANGE COLUMN `pd_dob` `pd_dob` DATE NULL DEFAULT NULL AFTER `ssn`,
                CHANGE COLUMN `specific_dob` `specific_dob` DATE NULL DEFAULT NULL AFTER `specific_gender`;
        ");
	}
}
