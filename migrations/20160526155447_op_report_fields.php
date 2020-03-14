<?php

use \Console\Migration\BaseMigration;

class OpReportFields extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case_op_report`
                CHANGE COLUMN `specimens_removed` `specimens_removed` MEDIUMTEXT NULL AFTER `operation_time`,
                CHANGE COLUMN `anesthesia_administered` `anesthesia_administered` TEXT NULL DEFAULT NULL AFTER `specimens_removed`,
                CHANGE COLUMN `ebl` `ebl` TEXT NULL DEFAULT NULL AFTER `anesthesia_administered`,
                CHANGE COLUMN `blood_transfused` `blood_transfused` TEXT NULL DEFAULT NULL AFTER `ebl`,
                CHANGE COLUMN `fluids` `fluids` TEXT NULL DEFAULT NULL AFTER `blood_transfused`,
                CHANGE COLUMN `drains` `drains` TEXT NULL DEFAULT NULL AFTER `fluids`,
                CHANGE COLUMN `urine_output` `urine_output` TEXT NULL DEFAULT NULL AFTER `drains`,
                CHANGE COLUMN `total_tourniquet_time` `total_tourniquet_time` TEXT NULL DEFAULT NULL AFTER `urine_output`,
                CHANGE COLUMN `consent` `consent` MEDIUMTEXT NULL DEFAULT NULL AFTER `total_tourniquet_time`,
                CHANGE COLUMN `complications` `complications` MEDIUMTEXT NULL DEFAULT NULL AFTER `consent`,
                CHANGE COLUMN `clinical_history` `clinical_history` MEDIUMTEXT NULL DEFAULT NULL AFTER `complications`,
                CHANGE COLUMN `approach` `approach` MEDIUMTEXT NULL DEFAULT NULL AFTER `clinical_history`,
                CHANGE COLUMN `findings` `findings` MEDIUMTEXT NULL DEFAULT NULL AFTER `approach`,
                CHANGE COLUMN `description_procedure` `description_procedure` MEDIUMTEXT NULL DEFAULT NULL AFTER `findings`,
                CHANGE COLUMN `follow_up_care` `follow_up_care` MEDIUMTEXT NULL DEFAULT NULL AFTER `description_procedure`,
                CHANGE COLUMN `conditions_for_discharge` `conditions_for_discharge` MEDIUMTEXT NULL DEFAULT NULL AFTER `follow_up_care`;
        ");

        $this->query("
            ALTER TABLE `case_op_report_custom_field_value`
	          CHANGE COLUMN `value` `value` MEDIUMTEXT NULL DEFAULT NULL AFTER `report_id`;
        ");

       $this->query("
        ALTER TABLE `case_op_report_future`
            CHANGE COLUMN `anesthesia_administered` `anesthesia_administered` TEXT NULL DEFAULT NULL AFTER `cpt_id`,
            CHANGE COLUMN `ebl` `ebl` TEXT NULL DEFAULT NULL AFTER `anesthesia_administered`,
            CHANGE COLUMN `drains` `drains` TEXT NULL DEFAULT NULL AFTER `ebl`,
            CHANGE COLUMN `consent` `consent` MEDIUMTEXT NULL DEFAULT NULL AFTER `drains`,
            CHANGE COLUMN `complications` `complications` MEDIUMTEXT NULL DEFAULT NULL AFTER `consent`,
            CHANGE COLUMN `approach` `approach` MEDIUMTEXT NULL DEFAULT NULL AFTER `complications`,
            CHANGE COLUMN `description_procedure` `description_procedure` MEDIUMTEXT NULL DEFAULT NULL AFTER `approach`,
            CHANGE COLUMN `follow_up_care` `follow_up_care` MEDIUMTEXT NULL DEFAULT NULL AFTER `description_procedure`,
            CHANGE COLUMN `conditions_for_discharge` `conditions_for_discharge` MEDIUMTEXT NULL DEFAULT NULL AFTER `follow_up_care`,
            CHANGE COLUMN `specimens_removed` `specimens_removed` MEDIUMTEXT NULL DEFAULT NULL AFTER `updated`,
            CHANGE COLUMN `findings` `findings` MEDIUMTEXT NULL DEFAULT NULL AFTER `specimens_removed`,
            CHANGE COLUMN `urine_output` `urine_output` TEXT NULL DEFAULT NULL AFTER `findings`,
            CHANGE COLUMN `fluids` `fluids` TEXT NULL DEFAULT NULL AFTER `urine_output`,
            CHANGE COLUMN `blood_transfused` `blood_transfused` TEXT NULL DEFAULT NULL AFTER `fluids`,
            CHANGE COLUMN `total_tourniquet_time` `total_tourniquet_time` TEXT NULL DEFAULT NULL AFTER `blood_transfused`,
            CHANGE COLUMN `clinical_history` `clinical_history` TEXT NULL DEFAULT NULL AFTER `total_tourniquet_time`;
       ");

    }
}
