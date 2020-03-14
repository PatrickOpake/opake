<?php

use \Console\Migration\BaseMigration;

class UpdateStaffPrefCard extends BaseMigration
{
    public function change()
    {
        $this->query("
            DELETE FROM `pref_card_staff`;
            DELETE FROM `pref_card_staff_note`;
            DELETE FROM `pref_card_staff_item`;

            ALTER TABLE `pref_card_staff` DROP `user_id`,
                DROP `case_type_id`;
    		        
    		ALTER TABLE `pref_card_staff` ADD `name` VARCHAR(255) DEFAULT NULL AFTER `id`,
    		    ADD `user_id` INT(11) NOT NULL AFTER `id`,
    		    ADD `stages` VARCHAR(255) DEFAULT NULL;
    		    
            ALTER TABLE `pref_card_staff_note` ADD `name` VARCHAR(255) DEFAULT NULL AFTER `card_id`;
            
            ALTER TABLE `pref_card_staff_item` ADD `stage_id` INT(11) NULL DEFAULT NULL AFTER `card_id`,
                ADD `item_number` VARCHAR(255) DEFAULT NULL AFTER `card_id`,
    		    ADD `default_qty` INT(11) NULL DEFAULT NULL,
    		    ADD `actual_use` INT(11) NULL DEFAULT NULL,
    		    DROP `quantity`;
            ALTER TABLE pref_card_staff_item DROP INDEX inventory_id;
    		    
            CREATE TABLE IF NOT EXISTS `pref_card_staff_case_type` (
                `pref_card_staff_id` INT(11) NOT NULL,
                `type_id` INT(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `pref_card_staff_case_type` ADD UNIQUE KEY `uni` (`pref_card_staff_id`,`type_id`);
    	");
    }
}
