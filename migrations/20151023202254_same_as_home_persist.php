<?php

use \Console\Migration\BaseMigration;

class SameAsHomePersist extends BaseMigration
{
	public function change()
	{
		$this->query("
          ALTER TABLE `case_registration`
                ADD COLUMN `mailing_same_as_home` TINYINT NULL DEFAULT NULL AFTER `home_email`;
        ");

		$this->query("
          ALTER TABLE `patient`
                ADD COLUMN `mailing_same_as_home` TINYINT NULL DEFAULT NULL AFTER `home_email`;
        ");
	}
}
