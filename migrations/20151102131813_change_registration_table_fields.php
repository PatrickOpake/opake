<?php

use \Console\Migration\BaseMigration;

class ChangeRegistrationTableFields extends BaseMigration
{
	/**
	 * Change Method.
	 *
	 * Write your reversible migrations using this method.
	 *
	 * More information on writing migrations is available here:
	 * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
	 *
	 * The following commands can be used in this method and Phinx will
	 * automatically reverse them when rolling back:
	 *
	 *    createTable
	 *    renameTable
	 *    addColumn
	 *    renameColumn
	 *    addIndex
	 *    addForeignKey
	 *
	 * Remember to call "create()" or "update()" and NOT "save()" when working
	 * with the Table class.
	 */
	public function change()
	{
		$this->query("
            ALTER TABLE `case_registration`
		ADD `employer_phone` VARCHAR(40) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `last_name` VARCHAR(255) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `first_name` VARCHAR(255) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `middle_name` VARCHAR(255) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `suffix` TINYINT(4) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `dob` DATE NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `gender` TINYINT(4) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `ssn` VARCHAR (255) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `phone` VARCHAR(40) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `address` VARCHAR(255) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `apt_number` VARCHAR(255) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `country_id` INT(11) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `state_id` INT(11) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `city_id` INT(11) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `zip_code` VARCHAR (20) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `relationship_to_insured` TINYINT (4) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `type` TINYINT (4) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `policy_number` VARCHAR(40) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `group_number` VARCHAR(40) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `employers_name` VARCHAR(255) NULL;
            ALTER TABLE `case_registration_insurance`
		ADD `is_primary` TINYINT(1) NULL;

            ALTER TABLE `case_registration`
                CHANGE COLUMN `assignment_of_benefits` `assignment_of_benefits` VARCHAR(255) NULL DEFAULT NULL;
            ALTER TABLE `case_registration` ADD `assignment_of_benefits_uploaded` TIMESTAMP NULL DEFAULT NULL;
            ALTER TABLE `case_registration`
                CHANGE COLUMN `advanced_beneficiary_notice` `advanced_beneficiary_notice` VARCHAR(255) NULL DEFAULT NULL;
            ALTER TABLE `case_registration` ADD `advanced_beneficiary_notice_uploaded` TIMESTAMP NULL DEFAULT NULL;
            ALTER TABLE `case_registration`
                CHANGE COLUMN `consent_for_treatment` `consent_for_treatment` VARCHAR(255) NULL DEFAULT NULL;
            ALTER TABLE `case_registration` ADD `consent_for_treatment_uploaded` TIMESTAMP NULL DEFAULT NULL;
            ALTER TABLE `case_registration`
                CHANGE COLUMN `smoking_status` `smoking_status` VARCHAR(255) NULL DEFAULT NULL;
            ALTER TABLE `case_registration` ADD `smoking_status_uploaded` TIMESTAMP NULL DEFAULT NULL;
            ALTER TABLE `case_registration`
                CHANGE COLUMN `hipaa_acknowledgement` `hipaa_acknowledgement` VARCHAR(255) NULL DEFAULT NULL;
            ALTER TABLE `case_registration` ADD `hipaa_acknowledgement_uploaded` TIMESTAMP NULL DEFAULT NULL;

            ALTER TABLE `case_registration` DROP `mailing_same_as_home`, DROP `mailing_address`, DROP `mailing_apt_number`, DROP `mailing_city_id`, DROP `mailing_state_id`, DROP `mailing_zip_code`, DROP `mailing_country_id`;
            ALTER TABLE `case_registration` DROP `kin_name`, DROP `kin_phone`, DROP `kin_address`, DROP `kin_apt_number`, DROP `kin_city_id`, DROP `kin_state_id`, DROP `kin_zip_code`, DROP `kin_country_id`;
            ALTER TABLE `case_registration` DROP `school`;

            ALTER TABLE `patient_insurance`
		ADD `last_name` VARCHAR(255) NULL;
            ALTER TABLE `patient_insurance`
		ADD `first_name` VARCHAR(255) NULL;
            ALTER TABLE `patient_insurance`
		ADD `middle_name` VARCHAR(255) NULL;
            ALTER TABLE `patient_insurance`
		ADD `suffix` TINYINT(4) NULL;
            ALTER TABLE `patient_insurance`
		ADD `dob` DATE NULL;
            ALTER TABLE `patient_insurance`
		ADD `gender` TINYINT(4) NULL;
            ALTER TABLE `patient_insurance`
		ADD `ssn` VARCHAR (255) NULL;
            ALTER TABLE `patient_insurance`
		ADD `phone` VARCHAR(40) NULL;
            ALTER TABLE `patient_insurance`
		ADD `address` VARCHAR(255) NULL;
            ALTER TABLE `patient_insurance`
		ADD `apt_number` VARCHAR(255) NULL;
            ALTER TABLE `patient_insurance`
		ADD `country_id` INT(11) NULL;
            ALTER TABLE `patient_insurance`
		ADD `state_id` INT(11) NULL;
            ALTER TABLE `patient_insurance`
		ADD `city_id` INT(11) NULL;
            ALTER TABLE `patient_insurance`
		ADD `zip_code` VARCHAR (20) NULL;
            ALTER TABLE `patient_insurance`
		ADD `relationship_to_insured` TINYINT (4) NULL;
            ALTER TABLE `patient_insurance`
		ADD `type` TINYINT (4) NULL;
            ALTER TABLE `patient_insurance`
		ADD `policy_number` VARCHAR(40) NULL;
            ALTER TABLE `patient_insurance`
		ADD `group_number` VARCHAR(40) NULL;
            ALTER TABLE `patient_insurance`
		ADD `employers_name` VARCHAR(255) NULL;
            ALTER TABLE `patient_insurance`
		ADD `is_primary` TINYINT(1) NULL;
        ");
	}
}
