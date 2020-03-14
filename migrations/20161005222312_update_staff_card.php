<?php

use \Console\Migration\BaseMigration;

class UpdateStaffCard extends BaseMigration
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
		
    		ALTER TABLE `card_staff` 
			ADD `additional_note` TEXT DEFAULT NULL AFTER `state`,
			ADD `name` VARCHAR(255) DEFAULT NULL AFTER `id`,
    		    	ADD `stages` VARCHAR(255) DEFAULT NULL,
    		    	ADD `status` TINYINT(4) NULL DEFAULT '1';
    		    	
		ALTER TABLE `card_staff` DROP `state`;
		
	    	ALTER TABLE `card_staff_note` ADD `name` VARCHAR(255) DEFAULT NULL AFTER `card_id`;
	    	
	    	ALTER TABLE `card_staff_item` 
	    		ADD `stage_id` INT(11) NULL DEFAULT NULL AFTER `card_id`,
		    	ADD `item_number` VARCHAR(255) DEFAULT NULL AFTER `card_id`,
    		    	ADD `default_qty` INT(11) NULL DEFAULT NULL,
    		    	ADD `actual_use` INT(11) NULL DEFAULT NULL,
    		    	DROP `quantity`;
    		    	
		ALTER TABLE card_staff_item DROP INDEX inventory_id;
		ALTER TABLE `card_staff_item` CHANGE `inventory_id` `inventory_id` INT(11) NULL DEFAULT NULL;


    	");
    }
}
