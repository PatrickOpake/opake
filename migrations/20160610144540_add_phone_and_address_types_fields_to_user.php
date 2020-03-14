<?php

use \Console\Migration\BaseMigration;

class AddPhoneAndAddressTypesFieldsToUser extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `user` 
              ADD `phone_type` INT NULL DEFAULT NULL AFTER `phone`,
              ADD `address_type` INT NULL DEFAULT NULL AFTER `address`;
		");

    }
}
