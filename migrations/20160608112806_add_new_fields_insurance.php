<?php

use \Console\Migration\BaseMigration;

class AddNewFieldsInsurance extends BaseMigration
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
              ADD COLUMN `individual_remaining_1` DECIMAL(10,2) NULL DEFAULT NULL,
              ADD COLUMN `individual_remaining_2` DECIMAL(10,2) NULL DEFAULT NULL,
              ADD COLUMN `family_remaining_1` DECIMAL(10,2) NULL DEFAULT NULL,
              ADD COLUMN `family_remaining_2` DECIMAL(10,2) NULL DEFAULT NULL,
              ADD COLUMN `family_out_of_pocket_maximum` VARCHAR(255) NULL DEFAULT NULL;
              
            ALTER TABLE `patient_insurance`
              ADD COLUMN `individual_remaining_1` DECIMAL(10,2) NULL DEFAULT NULL,
              ADD COLUMN `individual_remaining_2` DECIMAL(10,2) NULL DEFAULT NULL,
              ADD COLUMN `family_remaining_1` DECIMAL(10,2) NULL DEFAULT NULL,
              ADD COLUMN `family_remaining_2` DECIMAL(10,2) NULL DEFAULT NULL,
              ADD COLUMN `family_out_of_pocket_maximum` VARCHAR(255) NULL DEFAULT NULL;
              
            ALTER TABLE `case_registration_insurance` CHANGE `out_of_pocket_maximum` `individual_out_of_pocket_maximum` VARCHAR(255) NULL DEFAULT NULL; 
            ALTER TABLE `patient_insurance` CHANGE `out_of_pocket_maximum` `individual_out_of_pocket_maximum` VARCHAR(255) NULL DEFAULT NULL;
        ");
    }
}
