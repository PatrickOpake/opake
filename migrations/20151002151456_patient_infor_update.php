<?php

use Phinx\Migration\AbstractMigration;

class PatientInforUpdate extends AbstractMigration
{

	public function change()
	{
		$this->query('
ALTER TABLE `patient` CHANGE `gender` `gender` TINYINT NOT NULL;
ALTER TABLE `case_registration` CHANGE `pd_gender` `pd_gender` TINYINT NULL DEFAULT NULL;
ALTER TABLE `case_registration` ADD `pd_dob` TIMESTAMP NULL AFTER `ssn`;
ALTER TABLE `case_registration` ADD `pd_race` TINYINT NULL AFTER `pd_gender`;
');
	}

}
