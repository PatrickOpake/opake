<?php

use \Console\Migration\BaseMigration;

class AuthorizationOrReferenceNumber extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `insurance_data_regular` ADD `authorization_or_referral_number` VARCHAR(255) NULL DEFAULT NULL;
		");
    }
}
