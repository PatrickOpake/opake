<?php

use \Console\Migration\BaseMigration;

class ChangeTimestampToDatetime extends BaseMigration
{
    public function change()
    {
        $this->query("
        ALTER TABLE `case`
            CHANGE `time_start` `time_start` DATETIME,     
            CHANGE `time_end` `time_end` DATETIME,     
            CHANGE `time_check_in` `time_check_in` DATETIME,     
            CHANGE `time_start_in_fact` `time_start_in_fact` DATETIME,     
            CHANGE `time_end_in_fact` `time_end_in_fact` DATETIME,     
            CHANGE `started_at` `started_at` DATETIME;    
            
        ALTER TABLE `case_blocking_item`
            CHANGE `start` `start` DATETIME NOT NULL,
            CHANGE `end` `end` DATETIME NOT NULL;
            
        ALTER TABLE `case_coding_occurrence`
            CHANGE `occurence_date` `occurence_date` DATETIME NULL DEFAULT NULL;
            
        ALTER TABLE `case_coding_procedure`
            CHANGE `date` `date` DATETIME NULL DEFAULT NULL;
            
        ALTER TABLE `case_coding_supply`
            CHANGE `date` `date` DATETIME NULL DEFAULT NULL;
            
        ALTER TABLE `case_note`
            CHANGE `time_add` `time_add` DATETIME NULL DEFAULT NULL;
            
        ALTER TABLE `order_outgoing`
            CHANGE `date` `date` DATETIME NULL DEFAULT NULL;
            
        ALTER TABLE `organization`
            CHANGE `time_create` `time_create` DATETIME NULL DEFAULT NULL;

        ALTER TABLE `user`
            CHANGE `time_create` `time_create` DATETIME NULL DEFAULT NULL,
            CHANGE `time_first_login` `time_first_login` DATETIME NULL DEFAULT NULL,
            CHANGE `time_last_login` `time_last_login` DATETIME NULL DEFAULT NULL,
            CHANGE `time_status_change` `time_status_change` DATETIME NULL DEFAULT NULL;

        ALTER TABLE `alert`
            CHANGE `date` `date` DATETIME NOT NULL;
        ALTER TABLE `alert_view`
            CHANGE `view_date` `view_date` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;
            
        ALTER TABLE `block_info`
            CHANGE `date` `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
            
        ALTER TABLE `case_discharge`
            CHANGE `uploaded` `uploaded` DATETIME NOT NULL;
            
        ALTER TABLE `site`
            CHANGE `time_create` `time_create` DATETIME NOT NULL;
            
        ALTER TABLE `case_op_report_future`
            CHANGE `updated` `updated` DATETIME NOT NULL;
            
        ALTER TABLE `case_report`
            CHANGE `date` `date` DATETIME NOT NULL;
            
        ALTER TABLE `inventory`
            CHANGE `time_create` `time_create` DATETIME NOT NULL,
            CHANGE `time_update` `time_update` DATETIME NULL DEFAULT NULL;
            
        ALTER TABLE `order`
            CHANGE `date` `date` DATETIME NOT NULL;
            
        ALTER TABLE `order_item`
            CHANGE `exp_date` `exp_date` DATETIME NOT NULL;
            
        ALTER TABLE `vendor`
            CHANGE `time_create` `time_create` DATETIME NOT NULL;
            
        ALTER TABLE `case_hp`
            CHANGE `uploaded` `uploaded` DATETIME NOT NULL;
            
        ");
    }
}
