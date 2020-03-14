<?php

use \Console\Migration\BaseMigration;

class NewInsuranceTab extends BaseMigration
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
            ALTER TABLE `case_registration_insurance`
		ADD COLUMN `address_insurance` VARCHAR(255) NULL;

            ALTER TABLE `patient_insurance`
		ADD COLUMN `address_insurance` VARCHAR(255) NULL;
		
            ALTER TABLE `case_registration`
                    ADD COLUMN `attorney_name` VARCHAR(255) NULL DEFAULT NULL,
                    ADD COLUMN `attorney_phone` VARCHAR(40) NULL DEFAULT NULL,
                    ADD COLUMN `work_comp_insurance_name` VARCHAR(255) NULL DEFAULT NULL,
                    ADD COLUMN `work_comp_adjusters_name` VARCHAR(255) NULL DEFAULT NULL,
                    ADD COLUMN `work_comp_claim` VARCHAR(255) NULL DEFAULT NULL,
                    ADD COLUMN `work_comp_adjuster_phone` VARCHAR(40) NULL DEFAULT NULL,
                    ADD COLUMN `work_comp_insurance_address` VARCHAR(255) NULL DEFAULT NULL,
                    ADD COLUMN `work_comp_city_id` INT(11) NULL DEFAULT NULL,
                    ADD COLUMN `work_comp_state_id` INT(11) NULL DEFAULT NULL,
                    ADD COLUMN `work_comp_zip` VARCHAR(20) NULL DEFAULT NULL,
                    ADD COLUMN `work_comp_accident_date` DATE NULL DEFAULT NULL;
                    
            ALTER TABLE `case_registration_insurance` DROP `employers_name`, DROP `ssn`;
            ALTER TABLE `patient_insurance` DROP `employers_name`, DROP `ssn`;
        ");
    }
}
