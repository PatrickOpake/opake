<?php

use \Console\Migration\BaseMigration;

class AppliedPaymentId extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `billing_payment_posting_entered_patient_payment` ADD `applied_payment_id` INT  UNSIGNED  NULL  DEFAULT NULL  AFTER `check_number`;
			ALTER TABLE `billing_payment_posting_entered_insurance_payment` ADD `applied_payment_id` INT  UNSIGNED  NULL  DEFAULT NULL  AFTER `applied_amount`;
		");
    }
}
