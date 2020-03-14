<?php

use \Console\Migration\BaseMigration;

class Adjustment extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `billing_payment_posting_entered_patient_payment`
                ADD COLUMN `adjustment_reason` TINYINT UNSIGNED NULL DEFAULT NULL AFTER `applied_payment_id`,
                ADD COLUMN `adjustment_custom_reason` VARCHAR(150) NULL DEFAULT NULL AFTER `adjustment_reason`;
        ");
    }
}
