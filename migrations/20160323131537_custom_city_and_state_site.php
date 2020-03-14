<?php

use \Console\Migration\BaseMigration;

class CustomCityAndStateSite extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `site`
                ADD COLUMN `pay_custom_city` VARCHAR(255) NULL DEFAULT NULL AFTER `pay_zip_code`,
                ADD COLUMN `pay_custom_state` VARCHAR(255) NULL DEFAULT NULL AFTER `pay_custom_city`,
                ADD COLUMN `custom_city` VARCHAR(255) NULL DEFAULT NULL AFTER `state_id`,
                ADD COLUMN `custom_state` VARCHAR(255) NULL DEFAULT NULL AFTER `custom_city`;
        ");
    }
}
