<?php

use \Console\Migration\BaseMigration;

class CaseRegistrationFiles extends BaseMigration
{
	public function change()
	{
		$this->query("
            CREATE TABLE `case_registration_documents` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `case_registration_id` INT(10) NULL DEFAULT NULL,
                `document_type` INT(10) NULL DEFAULT NULL,
                `uploaded_file_id` INT(10) NULL DEFAULT NULL,
                `uploaded_date` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");

		$this->query("
            ALTER TABLE `case_registration`
                DROP COLUMN `assignment_of_benefits_uploaded`,
                DROP COLUMN `advanced_beneficiary_notice_uploaded`,
                DROP COLUMN `consent_for_treatment_uploaded`,
                DROP COLUMN `smoking_status_uploaded`,
                DROP COLUMN `hipaa_acknowledgement_uploaded`,
                DROP COLUMN `assignment_of_benefits`,
                DROP COLUMN `advanced_beneficiary_notice`,
                DROP COLUMN `consent_for_treatment`,
                DROP COLUMN `smoking_status`,
                DROP COLUMN `hipaa_acknowledgement`
        ");
	}
}
