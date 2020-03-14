<?php

use \Console\Migration\BaseMigration;

class AllowForTextInputInsuranceFields extends BaseMigration
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
            ALTER TABLE `case_registration_insurance` CHANGE `out_of_pocket_maximum` `out_of_pocket_maximum`  VARCHAR(255) NULL DEFAULT NULL;
            ALTER TABLE `case_registration_insurance` CHANGE `yearly_maximum` `yearly_maximum`  VARCHAR(255) NULL DEFAULT NULL;
            ALTER TABLE `case_registration_insurance` CHANGE `lifetime_maximum` `lifetime_maximum`  VARCHAR(255) NULL DEFAULT NULL;
            
            ALTER TABLE `patient_insurance` CHANGE `out_of_pocket_maximum` `out_of_pocket_maximum`  VARCHAR(255) NULL DEFAULT NULL;
            ALTER TABLE `patient_insurance` CHANGE `yearly_maximum` `yearly_maximum`  VARCHAR(255) NULL DEFAULT NULL;
            ALTER TABLE `patient_insurance` CHANGE `lifetime_maximum` `lifetime_maximum`  VARCHAR(255) NULL DEFAULT NULL;
        ");
    }
}
