<?php

use \Console\Migration\BaseMigration;

class AddAdditionalPhoneFields extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `patient` ADD COLUMN `additional_phone` VARCHAR(40) NULL DEFAULT NULL AFTER `ec_phone_number`;
            ALTER TABLE `case_registration` ADD COLUMN `additional_phone` VARCHAR(40) NULL DEFAULT NULL AFTER `employer_phone`;
        ");
	}
}
