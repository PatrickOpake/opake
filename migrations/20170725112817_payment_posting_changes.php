<?php

use \Console\Migration\BaseMigration;

class PaymentPostingChanges extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `billing_payment_posting_applied_payment` DROP `is_co_pay`;
			ALTER TABLE `billing_payment_posting_applied_payment` ADD `insurance_payment_id` INT  UNSIGNED  NULL  DEFAULT NULL  AFTER `amount_posted`;
			ALTER TABLE `billing_payment_posting_applied_payment` ADD `patient_payment_id` INT  UNSIGNED  NULL  DEFAULT NULL  AFTER `insurance_payment_id`;
			ALTER TABLE `billing_payment_posting_entered_insurance_payment` DROP `applied_payment_id`;
			ALTER TABLE `billing_payment_posting_entered_patient_payment` DROP `applied_payment_id`;
			ALTER TABLE `billing_payment_posting_entered_patient_payment` DROP `patient_id`;
			ALTER TABLE `billing_payment_posting_entered_patient_payment` ADD `description` VARCHAR(1024)  NULL  DEFAULT NULL  AFTER `is_co_pay`;

		");
    }
}
