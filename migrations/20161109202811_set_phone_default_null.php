<?php

use \Console\Migration\BaseMigration;

class SetPhoneDefaultNull extends BaseMigration
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
    		UPDATE `case_registration` SET `employer_phone` = NULL WHERE `employer_phone` = '';
    		UPDATE `case_registration` SET `home_phone` = NULL WHERE `home_phone` = '';
    		UPDATE `case_registration` SET `additional_phone` = NULL WHERE `additional_phone` = '';
    		UPDATE `case_registration` SET `ec_phone_number` = NULL WHERE `ec_phone_number` = '';
    		UPDATE `case_registration` SET `auto_adjuster_phone` = NULL WHERE `auto_adjuster_phone` = '';
    		UPDATE `case_registration` SET `attorney_phone` = NULL WHERE `attorney_phone` = '';
    		UPDATE `case_registration` SET `work_comp_adjuster_phone` = NULL WHERE `work_comp_adjuster_phone` = '';

    		
    		UPDATE `patient` SET `employer_phone` = NULL WHERE `employer_phone` = '';
    		UPDATE `patient` SET `home_phone` = NULL WHERE `home_phone` = '';
    		UPDATE `patient` SET `additional_phone` = NULL WHERE `additional_phone` = '';
    		UPDATE `patient` SET `ec_phone_number` = NULL WHERE `ec_phone_number` = '';
    		
		UPDATE `booking_patient` SET `employer_phone` = NULL WHERE `employer_phone` = '';
    		UPDATE `booking_patient` SET `home_phone` = NULL WHERE `home_phone` = '';
    		UPDATE `booking_patient` SET `additional_phone` = NULL WHERE `additional_phone` = '';
    		UPDATE `booking_patient` SET `ec_phone_number` = NULL WHERE `ec_phone_number` = '';
    		
    		UPDATE `case` SET `accompanied_phone` = NULL WHERE `accompanied_phone` = '';
    		
    		UPDATE `booking_sheet` SET `auto_adjuster_phone` = NULL WHERE `auto_adjuster_phone` = '';
    		UPDATE `booking_sheet` SET `attorney_phone` = NULL WHERE `attorney_phone` = '';
    		UPDATE `booking_sheet` SET `work_comp_adjuster_phone` = NULL WHERE `work_comp_adjuster_phone` = '';
    		
    		UPDATE `case_claim` SET `provider_phone` = NULL WHERE `provider_phone` = '';
    		
    		UPDATE `insurance_data_regular` SET `provider_phone` = NULL WHERE `provider_phone` = '';
    		UPDATE `insurance_data_regular` SET `phone` = NULL WHERE `phone` = '';
    	");
    }
}
