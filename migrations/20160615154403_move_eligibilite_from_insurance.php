<?php

use \Console\Migration\BaseMigration;

class MoveEligibiliteFromInsurance extends BaseMigration
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
            

            CREATE TABLE `case_registration_case_type` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `reg_id` INT(11) NOT NULL,
                `case_type_id` INT(11) NULL DEFAULT NULL,
                `is_pre_authorization` TINYINT(1) NULL DEFAULT '0',
                `pre_authorization` VARCHAR(255) NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;

            ALTER TABLE `case_registration`
                  ADD COLUMN `coverage_type` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `effective_date` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `term_date` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `renewal_date` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `individual_out_of_pocket_maximum` VARCHAR(255) NULL DEFAULT NULL,
                  ADD COLUMN `is_oon_benefits_cap` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `oon_benefits_cap` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `is_asc_benefits_cap` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `asc_benefits_cap` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `is_pre_existing_clauses` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `pre_existing_clauses` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `body_part` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `is_clauses_pertaining` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `subscribers_name` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `authorization_number` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `expiration` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `spoke_with` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `reference_number` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `staff_member_name` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `date` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `oon_benefits` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `pre_certification_required` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `pre_certification_obtained` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `self_funded` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `co_pay` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `co_insurance` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `patients_responsibility` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `individual_deductible` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `individual_met_to_date` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `family_deductible` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `family_met_to_date` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `yearly_maximum` VARCHAR(255) NULL DEFAULT NULL,
                  ADD COLUMN `lifetime_maximum` VARCHAR(255) NULL DEFAULT NULL,
                  ADD COLUMN `oon_reimbursement` TINYINT NULL DEFAULT NULL,
                  ADD COLUMN `individual_remaining_1` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `individual_remaining_2` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `family_remaining_1` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `family_remaining_2` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `family_out_of_pocket_maximum` VARCHAR(255) NULL DEFAULT NULL;


              ALTER TABLE `patient`
                  ADD COLUMN `coverage_type` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `effective_date` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `term_date` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `renewal_date` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `individual_out_of_pocket_maximum` VARCHAR(255) NULL DEFAULT NULL,
                  ADD COLUMN `is_oon_benefits_cap` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `oon_benefits_cap` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `is_asc_benefits_cap` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `asc_benefits_cap` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `is_pre_existing_clauses` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `pre_existing_clauses` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `body_part` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `is_clauses_pertaining` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `subscribers_name` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `authorization_number` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `expiration` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `spoke_with` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `reference_number` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `staff_member_name` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `date` VARCHAR (255) NULL DEFAULT NULL,
                  ADD COLUMN `oon_benefits` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `pre_certification_required` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `pre_certification_obtained` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `self_funded` TINYINT NULL DEFAULT '0',
                  ADD COLUMN `co_pay` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `co_insurance` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `patients_responsibility` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `individual_deductible` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `individual_met_to_date` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `family_deductible` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `family_met_to_date` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `yearly_maximum` VARCHAR(255) NULL DEFAULT NULL,
                  ADD COLUMN `lifetime_maximum` VARCHAR(255) NULL DEFAULT NULL,
                  ADD COLUMN `oon_reimbursement` TINYINT NULL DEFAULT NULL,
                  ADD COLUMN `individual_remaining_1` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `individual_remaining_2` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `family_remaining_1` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `family_remaining_2` DECIMAL(10,2) NULL DEFAULT NULL,
                  ADD COLUMN `family_out_of_pocket_maximum` VARCHAR(255) NULL DEFAULT NULL;

        ");

        $q = $this->getDb()->query('select')->table('case_registration')
                ->fields('id')
                ->execute();

        foreach ($q as $row) {
            $q_insurances = $this->getDb()->query('select')->table('case_registration_insurance')
                    ->where('registration_id', $row->id)
                    ->execute();

            $q_insurances = $q_insurances->as_array();
            if (isset($q_insurances[0])) {
                $insurance = (array)$q_insurances[0];
                $this->getDb()->query('update')->table('case_registration')
                        ->data([
                                'oon_benefits' => $insurance['oon_benefits'],
                                'pre_certification_required' => $insurance['pre_certification_required'],
                                'pre_certification_obtained' => $insurance['pre_certification_obtained'],
                                'self_funded' => $insurance['self_funded'],
                                'co_pay' => $insurance['co_pay'],
                                'co_insurance' => $insurance['co_insurance'],
                                'patients_responsibility' => $insurance['patients_responsibility'],
                                'individual_deductible' => $insurance['individual_deductible'],
                                'individual_met_to_date' => $insurance['individual_met_to_date'],
                                'individual_remaining_1' => $insurance['individual_remaining_1'],
                                'individual_remaining_2' => $insurance['individual_remaining_2'],
                                'individual_out_of_pocket_maximum' => $insurance['individual_out_of_pocket_maximum'],
                                'family_deductible' => $insurance['family_deductible'],
                                'family_met_to_date' => $insurance['family_met_to_date'],
                                'family_remaining_1' => $insurance['family_remaining_1'],
                                'family_remaining_2' => $insurance['family_remaining_2'],
                                'family_out_of_pocket_maximum' => $insurance['family_out_of_pocket_maximum'],
                                'yearly_maximum' => $insurance['yearly_maximum'],
                                'lifetime_maximum' => $insurance['lifetime_maximum'],
                                'coverage_type' => $insurance['coverage_type'],
                                'oon_reimbursement' => $insurance['oon_reimbursement'],
                                'effective_date' => $insurance['effective_date'],
                                'term_date' => $insurance['term_date'],
                                'renewal_date' => $insurance['renewal_date'],
                                'is_oon_benefits_cap' => $insurance['is_oon_benefits_cap'],
                                'oon_benefits_cap' => $insurance['oon_benefits_cap'],
                                'is_asc_benefits_cap' => $insurance['is_asc_benefits_cap'],
                                'asc_benefits_cap' => $insurance['asc_benefits_cap'],
                                'is_pre_existing_clauses' => $insurance['is_pre_existing_clauses'],
                                'pre_existing_clauses' => $insurance['pre_existing_clauses'],
                                'body_part' => $insurance['body_part'],
                                'is_clauses_pertaining' => $insurance['is_clauses_pertaining'],
                                'subscribers_name' => $insurance['subscribers_name'],
                                'authorization_number' => $insurance['authorization_number'],
                                'expiration' => $insurance['expiration'],
                                'spoke_with' => $insurance['spoke_with'],
                                'reference_number' => $insurance['reference_number'],
                                'staff_member_name' => $insurance['staff_member_name'],
                                'date' => $insurance['date'],
                        ])
                        ->where('id', $row->id)
                        ->execute();
            }
        }

        $q = $this->getDb()->query('select')->table('patient')
                ->fields('id')
                ->execute();

        foreach ($q as $row) {
            $q_insurances = $this->getDb()->query('select')->table('patient_insurance')
                    ->where('patient_id', $row->id)
                    ->execute();

            $q_insurances = $q_insurances->as_array();
            if (isset($q_insurances[0])) {
                $insurance = (array)$q_insurances[0];
                $this->getDb()->query('update')->table('patient')
                        ->data([
                                'oon_benefits' => $insurance['oon_benefits'],
                                'pre_certification_required' => $insurance['pre_certification_required'],
                                'pre_certification_obtained' => $insurance['pre_certification_obtained'],
                                'self_funded' => $insurance['self_funded'],
                                'co_pay' => $insurance['co_pay'],
                                'co_insurance' => $insurance['co_insurance'],
                                'patients_responsibility' => $insurance['patients_responsibility'],
                                'individual_deductible' => $insurance['individual_deductible'],
                                'individual_met_to_date' => $insurance['individual_met_to_date'],
                                'individual_remaining_1' => $insurance['individual_remaining_1'],
                                'individual_remaining_2' => $insurance['individual_remaining_2'],
                                'individual_out_of_pocket_maximum' => $insurance['individual_out_of_pocket_maximum'],
                                'family_deductible' => $insurance['family_deductible'],
                                'family_met_to_date' => $insurance['family_met_to_date'],
                                'family_remaining_1' => $insurance['family_remaining_1'],
                                'family_remaining_2' => $insurance['family_remaining_2'],
                                'family_out_of_pocket_maximum' => $insurance['family_out_of_pocket_maximum'],
                                'yearly_maximum' => $insurance['yearly_maximum'],
                                'lifetime_maximum' => $insurance['lifetime_maximum'],
                                'coverage_type' => $insurance['coverage_type'],
                                'oon_reimbursement' => $insurance['oon_reimbursement'],
                                'effective_date' => $insurance['effective_date'],
                                'term_date' => $insurance['term_date'],
                                'renewal_date' => $insurance['renewal_date'],
                                'is_oon_benefits_cap' => $insurance['is_oon_benefits_cap'],
                                'oon_benefits_cap' => $insurance['oon_benefits_cap'],
                                'is_asc_benefits_cap' => $insurance['is_asc_benefits_cap'],
                                'asc_benefits_cap' => $insurance['asc_benefits_cap'],
                                'is_pre_existing_clauses' => $insurance['is_pre_existing_clauses'],
                                'pre_existing_clauses' => $insurance['pre_existing_clauses'],
                                'body_part' => $insurance['body_part'],
                                'is_clauses_pertaining' => $insurance['is_clauses_pertaining'],
                                'subscribers_name' => $insurance['subscribers_name'],
                                'authorization_number' => $insurance['authorization_number'],
                                'expiration' => $insurance['expiration'],
                                'spoke_with' => $insurance['spoke_with'],
                                'reference_number' => $insurance['reference_number'],
                                'staff_member_name' => $insurance['staff_member_name'],
                                'date' => $insurance['date'],
                        ])
                        ->where('id', $row->id)
                        ->execute();
            }
        }


        $this->query("
                  
                  ALTER TABLE `case_registration_insurance`
                    DROP `oon_benefits`,
                    DROP `pre_certification_required`,
                    DROP `pre_certification_obtained`,
                    DROP `self_funded`,
                    DROP `co_pay`,
                    DROP `co_insurance`,
                    DROP `patients_responsibility`,
                    DROP `individual_deductible`,
                    DROP `individual_met_to_date`,
                    DROP `individual_remaining_1`,
                    DROP `individual_remaining_2`,
                    DROP `individual_out_of_pocket_maximum`,
                    DROP `family_deductible`,
                    DROP `family_met_to_date`,
                    DROP `family_remaining_1`,
                    DROP `family_remaining_2`,
                    DROP `family_out_of_pocket_maximum`,
                    DROP `yearly_maximum`,
                    DROP `lifetime_maximum`,
                    DROP `coverage_type`,
                    DROP `oon_reimbursement`,
                    DROP `effective_date`,
                    DROP `term_date`,
                    DROP `renewal_date`,
                    DROP `is_oon_benefits_cap`,
                    DROP `oon_benefits_cap`,
                    DROP `is_asc_benefits_cap`,
                    DROP `asc_benefits_cap`,
                    DROP `is_pre_existing_clauses`,
                    DROP `pre_existing_clauses`,
                    DROP `body_part`,
                    DROP `is_clauses_pertaining`,
                    DROP `subscribers_name`,
                    DROP `authorization_number`,
                    DROP `expiration`,
                    DROP `spoke_with`,
                    DROP `reference_number`,
                    DROP `staff_member_name`,
                    DROP `date`;

                  ALTER TABLE `patient_insurance`
                    DROP `oon_benefits`,
                    DROP `pre_certification_required`,
                    DROP `pre_certification_obtained`,
                    DROP `self_funded`,
                    DROP `co_pay`,
                    DROP `co_insurance`,
                    DROP `patients_responsibility`,
                    DROP `individual_deductible`,
                    DROP `individual_met_to_date`,
                    DROP `individual_remaining_1`,
                    DROP `individual_remaining_2`,
                    DROP `individual_out_of_pocket_maximum`,
                    DROP `family_deductible`,
                    DROP `family_met_to_date`,
                    DROP `family_remaining_1`,
                    DROP `family_remaining_2`,
                    DROP `family_out_of_pocket_maximum`,
                    DROP `yearly_maximum`,
                    DROP `lifetime_maximum`,
                    DROP `coverage_type`,
                    DROP `oon_reimbursement`,
                    DROP `effective_date`,
                    DROP `term_date`,
                    DROP `renewal_date`,
                    DROP `is_oon_benefits_cap`,
                    DROP `oon_benefits_cap`,
                    DROP `is_asc_benefits_cap`,
                    DROP `asc_benefits_cap`,
                    DROP `is_pre_existing_clauses`,
                    DROP `pre_existing_clauses`,
                    DROP `body_part`,
                    DROP `is_clauses_pertaining`,
                    DROP `subscribers_name`,
                    DROP `authorization_number`,
                    DROP `expiration`,
                    DROP `spoke_with`,
                    DROP `reference_number`,
                    DROP `staff_member_name`,
                    DROP `date`;
        ");
    }
}