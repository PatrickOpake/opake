<?php

use \Console\Migration\BaseMigration;

class UpdateOutOfNetwork extends BaseMigration
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
             ALTER TABLE `patient_insurance`
              ADD COLUMN `coverage_type` VARCHAR (255) NULL DEFAULT NULL,
              ADD COLUMN `effective_date` VARCHAR (255) NULL DEFAULT NULL,
              ADD COLUMN `term_date` VARCHAR (255) NULL DEFAULT NULL,
              ADD COLUMN `renewal_date` VARCHAR (255) NULL DEFAULT NULL,
              ADD COLUMN `provider_phone` VARCHAR (40) NULL DEFAULT NULL,
              ADD COLUMN `out_of_pocket_maximum` DECIMAL(10,2) NULL DEFAULT NULL,
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
              ADD COLUMN `date` VARCHAR (255) NULL DEFAULT NULL;

            ALTER TABLE `case_registration_insurance`
              ADD COLUMN `coverage_type` VARCHAR (255) NULL DEFAULT NULL,
              ADD COLUMN `effective_date` VARCHAR (255) NULL DEFAULT NULL,
              ADD COLUMN `term_date` VARCHAR (255) NULL DEFAULT NULL,
              ADD COLUMN `renewal_date` VARCHAR (255) NULL DEFAULT NULL,
              ADD COLUMN `provider_phone` VARCHAR (40) NULL DEFAULT NULL,
              ADD COLUMN `out_of_pocket_maximum` DECIMAL(10,2) NULL DEFAULT NULL,
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
              ADD COLUMN `date` VARCHAR (255) NULL DEFAULT NULL;

            ALTER TABLE `patient_insurance`
              DROP COLUMN `pre_certification_contact_name`,
              DROP COLUMN `pre_certification`,
              DROP COLUMN `oon_phone`;

            ALTER TABLE `case_registration_insurance`
              DROP COLUMN `pre_certification_contact_name`,
              DROP COLUMN `pre_certification`,
              DROP COLUMN `oon_phone`;

            CREATE TABLE IF NOT EXISTS `case_registration_insurance_cpt` (
                    `id` int(11) NOT NULL,
                    `insurance_id` int(11) NOT NULL,
                    `cpt_id` int(11) NULL,
                    `is_pre_authorization` tinyint(1) NULL DEFAULT '0',
                    `pre_authorization` VARCHAR(255) NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ALTER TABLE `case_registration_insurance_cpt` ADD PRIMARY KEY (`id`);
                ALTER TABLE `case_registration_insurance_cpt` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");
    }
}
