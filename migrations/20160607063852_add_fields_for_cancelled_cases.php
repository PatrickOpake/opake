<?php

use \Console\Migration\BaseMigration;

class AddFieldsForCancelledCases extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case`
              ADD COLUMN `cancel_time` DATETIME NULL DEFAULT NULL AFTER `appointment_status`,
              ADD COLUMN `cancel_status` INT NULL DEFAULT NULL AFTER `appointment_status`,
              ADD COLUMN `cancel_reason` VARCHAR (255) NULL DEFAULT NULL AFTER `appointment_status`,
              ADD COLUMN `canceled_user_id` INT NULL DEFAULT NULL AFTER `appointment_status`;
        ");
    }
}
