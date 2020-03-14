<?php

use \Console\Migration\BaseMigration;

class ImproveInsuranceCard extends BaseMigration
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
		ALTER TABLE `insurance_data_workers_comp` 
			ADD COLUMN `insurance_company_phone` VARCHAR(40) NULL DEFAULT NULL,
			ADD COLUMN `authorization_number` VARCHAR (255) NULL DEFAULT NULL;
		ALTER TABLE `insurance_data_auto_accident` 
			ADD COLUMN `insurance_company_phone` VARCHAR(40) NULL DEFAULT NULL,
			ADD COLUMN `authorization_number` VARCHAR (255) NULL DEFAULT NULL;
			
		ALTER TABLE `booking_sheet`
			ADD COLUMN `auto_insurance_company_phone` VARCHAR(40) NULL DEFAULT NULL,
			ADD COLUMN `auto_insurance_authorization_number` VARCHAR (255) NULL DEFAULT NULL,
			ADD COLUMN `work_comp_insurance_company_phone` VARCHAR (40) NULL DEFAULT NULL,
			ADD COLUMN `work_comp_authorization_number` VARCHAR (255) NULL DEFAULT NULL;
			
		ALTER TABLE `case_registration`
			ADD COLUMN `auto_insurance_company_phone` VARCHAR(40) NULL DEFAULT NULL,
			ADD COLUMN `auto_insurance_authorization_number` VARCHAR (255) NULL DEFAULT NULL,
			ADD COLUMN `work_comp_insurance_company_phone` VARCHAR (40) NULL DEFAULT NULL,
			ADD COLUMN `work_comp_authorization_number` VARCHAR (255) NULL DEFAULT NULL;
	");
    }
}
