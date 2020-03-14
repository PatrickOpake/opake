<?php

use Phinx\Migration\AbstractMigration;

class PatientAndRegistrationTitle extends AbstractMigration
{

	public function change()
	{
		$this->query('
			ALTER TABLE `patient` ADD `title` TINYINT NULL AFTER `city_id`;
			ALTER TABLE `patient` CHANGE `suffix` `suffix` TINYINT(4) NULL DEFAULT NULL AFTER `last_name`;

			ALTER TABLE `case_registration` ADD `title` TINYINT NULL AFTER `status`;
			ALTER TABLE `case_registration` CHANGE `suffix` `suffix` TINYINT NULL DEFAULT NULL AFTER `pd_last_name`;
');
	}

}
