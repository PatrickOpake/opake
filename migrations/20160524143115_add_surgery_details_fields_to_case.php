<?php

use \Console\Migration\BaseMigration;

class AddSurgeryDetailsFieldsToCase extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case`
              ADD COLUMN `pre_op_none` TINYINT NULL DEFAULT '0',
              ADD COLUMN `pre_op_medical_clearance` TINYINT NULL DEFAULT '0',
              ADD COLUMN `pre_op_labs` TINYINT NULL DEFAULT '0',
              ADD COLUMN `pre_op_xray` TINYINT NULL DEFAULT '0',
              ADD COLUMN `pre_op_ekg` TINYINT NULL DEFAULT '0',
              ADD COLUMN `studies_ordered_none` TINYINT NULL DEFAULT '0',
              ADD COLUMN `studies_ordered_cbc` TINYINT NULL DEFAULT '0',
              ADD COLUMN `studies_ordered_chems` TINYINT NULL DEFAULT '0',
              ADD COLUMN `studies_ordered_ekg` TINYINT NULL DEFAULT '0',
              ADD COLUMN `studies_ordered_pt_pit` TINYINT NULL DEFAULT '0',
              ADD COLUMN `studies_ordered_cxr` TINYINT NULL DEFAULT '0',
              ADD COLUMN `studies_ordered_lft` TINYINT NULL DEFAULT '0',
              ADD COLUMN `studies_ordered_dig_level` TINYINT NULL DEFAULT '0',
              ADD COLUMN `studies_ordered_other` TINYINT NULL DEFAULT '0',
              ADD COLUMN `studies_other` VARCHAR (255) NULL DEFAULT NULL,              
              ADD COLUMN `anesthesia_type` INT NULL DEFAULT NULL,
              ADD COLUMN `anesthesia_other` VARCHAR (255) NULL DEFAULT NULL,
              ADD COLUMN `special_equipment_required` TINYINT NULL DEFAULT '0',
              ADD COLUMN `special_equipment_implants` VARCHAR (255) NULL DEFAULT NULL;
              
            CREATE TABLE IF NOT EXISTS `case_surgeon_assistant` (
                `surgeon_assistant_id` int(11) NOT NULL,
                `case_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_surgeon_assistant` ADD UNIQUE KEY `uni` (`surgeon_assistant_id`,`case_id`);
        ");
    }
}
