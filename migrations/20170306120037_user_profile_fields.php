<?php

use \Console\Migration\BaseMigration;

class UserProfileFields extends BaseMigration
{
    public function change()
    {
	    $this->query("ALTER TABLE `user` ADD `npi` VARCHAR(10) NULL DEFAULT NULL");
	    $this->query("ALTER TABLE `user` ADD `tin` VARCHAR(50) NULL DEFAULT NULL");
	    $this->query("ALTER TABLE `user` ADD `insurance_provider_number` VARCHAR(50) NULL DEFAULT NULL");
	    $this->query("ALTER TABLE `user` ADD `taxonomy_code` VARCHAR(50) NULL DEFAULT NULL");
    }
}
