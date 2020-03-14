<?php

use \Console\Migration\BaseMigration;

class AddPointOfContact extends BaseMigration
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
    		ALTER TABLE `case_registration` ADD COLUMN `point_of_contact_phone` varchar(40) DEFAULT NULL;
    		ALTER TABLE `case_registration` ADD COLUMN `point_of_contact_phone_type` tinyint(4) DEFAULT NULL;
    		
		ALTER TABLE `patient` ADD COLUMN `point_of_contact_phone` varchar(40) DEFAULT NULL;
    		ALTER TABLE `patient` ADD COLUMN `point_of_contact_phone_type` tinyint(4) DEFAULT NULL;
    		
    		ALTER TABLE `booking_patient` ADD COLUMN `point_of_contact_phone` varchar(40) DEFAULT NULL;
    		ALTER TABLE `booking_patient` ADD COLUMN `point_of_contact_phone_type` tinyint(4) DEFAULT NULL;x``
    	");
    }
}
