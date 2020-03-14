<?php

use \Console\Migration\BaseMigration;

class PatientDateOfBirthDate extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `patient`
              CHANGE COLUMN `dob` `dob` DATE NULL DEFAULT NULL AFTER `ssn`;
              ");
	}
}
