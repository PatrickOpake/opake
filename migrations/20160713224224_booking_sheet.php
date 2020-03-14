<?php

use \Console\Migration\BaseMigration;

class BookingSheet extends BaseMigration
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
            ALTER TABLE `case_registration` ADD `parents_name` VARCHAR(255) NULL DEFAULT NULL AFTER `middle_name`;
            ALTER TABLE `patient` ADD `parents_name` VARCHAR(255) NULL DEFAULT NULL AFTER `middle_name`;            
            
            ALTER TABLE `case_registration` ADD `home_phone_type` TINYINT(4) NULL DEFAULT NULL;
            ALTER TABLE `patient` ADD `home_phone_type` TINYINT(4) NULL DEFAULT NULL;
            
            ALTER TABLE `case_registration` ADD `additional_phone_type` TINYINT(4) NULL DEFAULT NULL;
            ALTER TABLE `patient` ADD `additional_phone_type` TINYINT(4) NULL DEFAULT NULL;  
            
            ALTER TABLE `case_registration` ADD `ec_phone_type` TINYINT(4) NULL DEFAULT NULL;
            ALTER TABLE `patient` ADD `ec_phone_type` TINYINT(4) NULL DEFAULT NULL;  
            
            ALTER TABLE `case_registration` ADD `relationship` TINYINT(4) NULL DEFAULT NULL;
            ALTER TABLE `patient` ADD `relationship` TINYINT(4) NULL DEFAULT NULL;
            
            ALTER TABLE `case` ADD `locate` TINYINT(4) NULL DEFAULT NULL;
            ALTER TABLE `case` ADD `pre_op_data_required` TINYINT(4) NULL DEFAULT NULL;
            ALTER TABLE `case` ADD `studies_ordered` TINYINT(4) NULL DEFAULT NULL;
            ALTER TABLE `case` ADD `transportation` TINYINT(4) NULL DEFAULT NULL;
            ALTER TABLE `case` ADD `transportation_notes` VARCHAR(255) NULL DEFAULT NULL;
            
            CREATE TABLE `booking_sheet` (
                `id` INT(10) AUTO_INCREMENT,
                `organization_id` INT(10) NULL DEFAULT NULL,
                `patient_id` INT(10) NULL DEFAULT NULL,
                `status` tinyint(4) NOT NULL DEFAULT '0',
                `time_start` 	DATETIME NULL DEFAULT NULL,
                `time_end` DATETIME NULL DEFAULT NULL,
                `room_id` int(11) NOT NULL,
                `pre_op_data_required` tinyint(4) NULL DEFAULT NULL,
                `location` tinyint(4) NULL DEFAULT NULL,
                `studies_ordered` tinyint(4) NULL DEFAULT NULL,
                `studies_other` varchar(255) DEFAULT NULL,
                `anesthesia_type` int(11) NULL DEFAULT NULL,
                `anesthesia_other` varchar(255) DEFAULT NULL,
                `special_equipment_implants` varchar(255) DEFAULT NULL,
                `transportation` tinyint(4) DEFAULT NULL,
                `transportation_notes` varchar(255) DEFAULT NULL,
                `description` text,
                `patients_relations` tinyint(4) NULL DEFAULT NULL,
                `auto_insurance_name` varchar(255) DEFAULT NULL,
                `auto_adjust_name` varchar(255) DEFAULT NULL,
                `auto_claim` varchar(255) DEFAULT NULL,
                `auto_adjuster_phone` varchar(40) DEFAULT NULL,
                `auto_insurance_address` varchar(255) DEFAULT NULL,
                `auto_city_id` int(11) DEFAULT NULL,
                `auto_state_id` int(11) DEFAULT NULL,
                `auto_zip` varchar(20) DEFAULT NULL,
                `accident_date` date DEFAULT NULL,
                `attorney_name` varchar(255) DEFAULT NULL,
                `attorney_phone` varchar(40) DEFAULT NULL,
                `auto_is_primary` tinyint(1) DEFAULT NULL,                
                `work_comp_insurance_name` varchar(255) DEFAULT NULL,
                `work_comp_adjusters_name` varchar(255) DEFAULT NULL,
                `work_comp_claim` varchar(255) DEFAULT NULL,
                `work_comp_adjuster_phone` varchar(40) DEFAULT NULL,
                `work_comp_insurance_address` varchar(255) DEFAULT NULL,
                `work_comp_city_id` int(11) DEFAULT NULL,
                `work_comp_state_id` int(11) DEFAULT NULL,
                `work_comp_zip` varchar(20) DEFAULT NULL,
                `work_comp_accident_date` date DEFAULT NULL,
                `work_comp_is_primary` tinyint(1) DEFAULT NULL,
                
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;
            
            CREATE TABLE `booking_user` (
              `booking_id` int(11) NOT NULL,
              `user_id` int(11) NOT NULL,
              `active` tinyint(1) NOT NULL DEFAULT '1',
              `order` smallint(6) DEFAULT '0'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
             CREATE TABLE IF NOT EXISTS `booking_additional_type` (
                `type_id` int(11) NOT NULL,
                `booking_id` int(11) NOT NULL,
                `order` TINYINT(3) UNSIGNED NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `booking_additional_type` ADD UNIQUE KEY `uni` (`type_id`,`booking_id`);
            
            CREATE TABLE IF NOT EXISTS `booking_admitting_diagnosis` (
                `id` int(11) NOT NULL,
                `booking_id` int(11) NOT NULL,
                `diagnosis_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `booking_admitting_diagnosis`
            ADD PRIMARY KEY (`id`),
            ADD KEY `booking_id` (`booking_id`),
            ADD KEY `diagnosis_id` (`diagnosis_id`);
            ALTER TABLE `booking_admitting_diagnosis` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            
            
            CREATE TABLE `booking_insurance` (
              `id` int(11) NOT NULL,
              `booking_id` int(11) NOT NULL,
              `insurance_id` int(11) DEFAULT NULL,
              `last_name` varchar(255) DEFAULT NULL,
              `first_name` varchar(255) DEFAULT NULL,
              `middle_name` varchar(255) DEFAULT NULL,
              `suffix` tinyint(4) DEFAULT NULL,
              `dob` date DEFAULT NULL,
              `gender` tinyint(4) DEFAULT NULL,
              `phone` varchar(40) DEFAULT NULL,
              `address` varchar(255) DEFAULT NULL,
              `apt_number` varchar(255) DEFAULT NULL,
              `country_id` int(11) DEFAULT NULL,
              `state_id` int(11) DEFAULT NULL,
              `custom_state` varchar(255) DEFAULT NULL,
              `city_id` int(11) DEFAULT NULL,
              `custom_city` varchar(255) DEFAULT NULL,
              `zip_code` varchar(20) DEFAULT NULL,
              `relationship_to_insured` tinyint(4) DEFAULT NULL,
              `type` tinyint(4) DEFAULT NULL,
              `policy_number` varchar(40) DEFAULT NULL,
              `group_number` varchar(40) DEFAULT NULL,
              `is_primary` tinyint(1) DEFAULT NULL,
              `provider_phone` varchar(40) DEFAULT NULL,
              `insurance_verified` tinyint(4) DEFAULT '0',
              `is_pre_authorization_completed` tinyint(4) DEFAULT '0',
              `address_insurance` varchar(255) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                
            ALTER TABLE `booking_insurance`
                  ADD PRIMARY KEY (`id`);
            ALTER TABLE `booking_insurance`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
              
            CREATE TABLE `booking_note` (
              `id` int(11) NOT NULL,
              `booking_id` int(11) NOT NULL,
              `user_id` int(11) NOT NULL,
              `time_add` datetime DEFAULT NULL,
              `text` text
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
            ALTER TABLE `booking_note`
              ADD PRIMARY KEY (`id`),
              ADD KEY `booking_id` (`booking_id`);
            
            ALTER TABLE `booking_note`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
              
              
              
          CREATE TABLE `user_booking_note` (
              `id` int(11) NOT NULL,
              `booking_id` int(11) NOT NULL,
              `user_id` int(11) NOT NULL,
              `last_read_note_id` int(11) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


            ALTER TABLE `user_booking_note`
              ADD PRIMARY KEY (`id`);
            
            ALTER TABLE `user_booking_note`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
              
            ALTER TABLE `booking_sheet` ADD COLUMN `notes_count` INT(11) NOT NULL DEFAULT 0;
            
            CREATE TABLE `case_in_service` (
              `id` int(11) NOT NULL,
              `start` datetime DEFAULT NULL,
              `end` datetime DEFAULT NULL,          
              `organization_id` int(11) NOT NULL,
              `location_id` int(11) NOT NULL,
              `description` text
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


        ALTER TABLE `case_in_service`
          ADD PRIMARY KEY (`id`),
          ADD KEY `organization_id` (`organization_id`),
          ADD KEY `start` (`start`),
          ADD KEY `end` (`end`);


        ALTER TABLE `case_in_service`
          MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;                    
                    
        ");


    }
}
