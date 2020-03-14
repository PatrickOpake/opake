<?php

use \Console\Migration\BaseMigration;

class AddInsuranceVerification extends BaseMigration
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
    public function up()
    {
		$this->query("
			CREATE TABLE `case_registration_insurance_verification` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `case_registration_id` INT(11) NOT NULL,
                `case_insurance_id` INT(11) NOT NULL,
                
                `insurance_verified` tinyint(1) DEFAULT '0',
                `is_pre_authorization_completed` tinyint(1) DEFAULT '0',
                `verification_status` TINYINT NULL DEFAULT '0',
                `verification_completed_date` DATETIME NULL DEFAULT NULL,
                
                `coverage_type` VARCHAR (255) NULL DEFAULT NULL,
				`effective_date` VARCHAR (255) NULL DEFAULT NULL,
				`term_date` VARCHAR (255) NULL DEFAULT NULL,
				`renewal_date` VARCHAR (255) NULL DEFAULT NULL,
				`individual_out_of_pocket_maximum` VARCHAR(255) NULL DEFAULT NULL,
				`is_oon_benefits_cap` TINYINT NULL DEFAULT '0',
				`oon_benefits_cap` VARCHAR (255) NULL DEFAULT NULL,
				`is_asc_benefits_cap` TINYINT NULL DEFAULT '0',
				`asc_benefits_cap` VARCHAR (255) NULL DEFAULT NULL,
				`is_pre_existing_clauses` TINYINT NULL DEFAULT '0',
				`pre_existing_clauses` VARCHAR (255) NULL DEFAULT NULL,
				`body_part` VARCHAR (255) NULL DEFAULT NULL,
				`is_clauses_pertaining` TINYINT NULL DEFAULT '0',
				`subscribers_name` VARCHAR (255) NULL DEFAULT NULL,
				`authorization_number` VARCHAR (255) NULL DEFAULT NULL,
				`expiration` VARCHAR (255) NULL DEFAULT NULL,
				`spoke_with` VARCHAR (255) NULL DEFAULT NULL,
				`reference_number` VARCHAR (255) NULL DEFAULT NULL,
				`staff_member_name` VARCHAR (255) NULL DEFAULT NULL,
				`date` VARCHAR (255) NULL DEFAULT NULL,
				`oon_benefits` TINYINT NULL DEFAULT '0',
				`pre_certification_required` TINYINT NULL DEFAULT '0',
				`pre_certification_obtained` TINYINT NULL DEFAULT '0',
				`self_funded` TINYINT NULL DEFAULT '0',
				`co_pay` DECIMAL(10,2) NULL DEFAULT NULL,
				`co_insurance` DECIMAL(10,2) NULL DEFAULT NULL,
				`patients_responsibility` DECIMAL(10,2) NULL DEFAULT NULL,
				`individual_deductible` DECIMAL(10,2) NULL DEFAULT NULL,
				`individual_met_to_date` DECIMAL(10,2) NULL DEFAULT NULL,
				`family_deductible` DECIMAL(10,2) NULL DEFAULT NULL,
				`family_met_to_date` DECIMAL(10,2) NULL DEFAULT NULL,
				`yearly_maximum` VARCHAR(255) NULL DEFAULT NULL,
				`lifetime_maximum` VARCHAR(255) NULL DEFAULT NULL,
				`oon_reimbursement` TINYINT NULL DEFAULT NULL,
				`individual_remaining_1` DECIMAL(10,2) NULL DEFAULT NULL,
				`individual_remaining_2` DECIMAL(10,2) NULL DEFAULT NULL,
				`family_remaining_1` DECIMAL(10,2) NULL DEFAULT NULL,
				`family_remaining_2` DECIMAL(10,2) NULL DEFAULT NULL,
				`family_out_of_pocket_maximum` VARCHAR(255) NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE (case_registration_id, case_insurance_id)
            )
            ENGINE=InnoDB;
		");

		$this->query("TRUNCATE TABLE case_registration_insurance_case_type");

		$this->query("ALTER TABLE case_registration_insurance_case_type CHANGE insurance_id verification_id INT (11) NOT NULL");
    }


    public function down()
	{
		$this->query('DROP TABLE case_registration_insurance_verification');
	}
}
