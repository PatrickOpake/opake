<?php

use \Console\Migration\BaseMigration;

class CodingInsurancePolicyFields extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `case_coding_insurance` ADD `policy_number` VARCHAR(40)  NULL  DEFAULT NULL;
			ALTER TABLE `case_coding_insurance` ADD `group_number` VARCHAR(40)  NULL  DEFAULT NULL;
			ALTER TABLE `case_coding_insurance` ADD `provider_phone` VARCHAR(40)  NULL  DEFAULT NULL;
			ALTER TABLE `case_coding_insurance` ADD `authorization_or_referral_number` VARCHAR(255)  NULL  DEFAULT NULL;
		");
    }
}
