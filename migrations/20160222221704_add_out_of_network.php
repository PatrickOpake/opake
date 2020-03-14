<?php

use \Console\Migration\BaseMigration;

class AddOutOfNetwork extends BaseMigration
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
              ADD COLUMN `yearly_maximum` DECIMAL(10,2) NULL DEFAULT NULL,
              ADD COLUMN `lifetime_maximum` DECIMAL(10,2) NULL DEFAULT NULL,
              ADD COLUMN `pre_certification_contact_name` VARCHAR (255) NULL DEFAULT NULL,
              ADD COLUMN `pre_certification` VARCHAR (255) NULL DEFAULT NULL,
              ADD COLUMN `oon_phone` VARCHAR (40) NULL DEFAULT NULL;

            ALTER TABLE `case_registration_insurance`
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
              ADD COLUMN `yearly_maximum` DECIMAL(10,2) NULL DEFAULT NULL,
              ADD COLUMN `lifetime_maximum` DECIMAL(10,2) NULL DEFAULT NULL,
              ADD COLUMN `pre_certification_contact_name` VARCHAR (255) NULL DEFAULT NULL,
              ADD COLUMN `pre_certification` VARCHAR (255) NULL DEFAULT NULL,
              ADD COLUMN `oon_phone` VARCHAR (40) NULL DEFAULT NULL;

        ");
	}
}
