<?php

use \Console\Migration\BaseMigration;

class NewItemCreation extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE IF NOT EXISTS `booking_equipment` (
                `inventory_id` int(11) NOT NULL,
                `booking_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `booking_equipment` ADD UNIQUE KEY `uni` (`inventory_id`,`booking_id`);
            
            CREATE TABLE IF NOT EXISTS `booking_implant` (
                `inventory_id` int(11) NOT NULL,
                `booking_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `booking_implant` ADD UNIQUE KEY `uni` (`inventory_id`,`booking_id`);
            
            CREATE TABLE IF NOT EXISTS `case_equipment` (
                `inventory_id` int(11) NOT NULL,
                `case_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_equipment` ADD UNIQUE KEY `uni` (`inventory_id`,`case_id`);
            
            CREATE TABLE IF NOT EXISTS `case_implant` (
                `inventory_id` int(11) NOT NULL,
                `case_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_implant` ADD UNIQUE KEY `uni` (`inventory_id`,`case_id`);
            
            ALTER TABLE `pref_card_stage` ADD `is_requested_items` tinyint(1) NULL DEFAULT 0;
            INSERT INTO `pref_card_stage` (name, is_requested_items) VALUES ('Requested Items', 1);
    	");
    }
}
