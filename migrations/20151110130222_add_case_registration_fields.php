<?php

use \Console\Migration\BaseMigration;

class AddCaseRegistrationFields extends BaseMigration
{
	public function change()
	{
		$this->query("ALTER TABLE `case_registration`
                ADD COLUMN `custom_home_state` VARCHAR(255) NULL DEFAULT NULL AFTER `home_state_id`,
                ADD COLUMN `custom_home_city` VARCHAR(255) NULL DEFAULT NULL AFTER `home_city_id`;
                ALTER TABLE `case_registration_insurance`
                ADD COLUMN `custom_state` VARCHAR(255) NULL DEFAULT NULL AFTER `state_id`,
                ADD COLUMN `custom_city` VARCHAR(255) NULL DEFAULT NULL AFTER `city_id`;
                ");
	}
}
