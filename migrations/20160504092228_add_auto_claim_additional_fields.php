<?php

use \Console\Migration\BaseMigration;

class AddAutoClaimAdditionalFields extends BaseMigration
{
    public function change()
    {
        $this->query("
          ALTER TABLE `case_registration`
                ADD COLUMN `auto_insurance_name` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `auto_adjust_name` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `auto_claim` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `auto_adjuster_phone` VARCHAR(40) NULL DEFAULT NULL,
                ADD COLUMN `auto_insurance_address` VARCHAR(255) NULL DEFAULT NULL,
                ADD COLUMN `auto_city_id` INT(11) NULL DEFAULT NULL,
                ADD COLUMN `auto_state_id` INT(11) NULL DEFAULT NULL,
                ADD COLUMN `auto_zip` VARCHAR(20) NULL DEFAULT NULL,
                ADD COLUMN `accident_date` DATE NULL DEFAULT NULL;
        ");
    }
}
