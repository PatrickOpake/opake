<?php

use \Console\Migration\BaseMigration;

class AddReportTemplateTable extends BaseMigration
{
	public function change()
	{
		$this->query("
            CREATE TABLE IF NOT EXISTS `case_op_report_fields_template` (
                `id` int(11) NOT NULL,
                `organization_id` int(11) NOT NULL,
                `report_id` INT(11) NULL,
                `case_id` INT(11) NULL,
                `dob` tinyint(1) NOT NULL DEFAULT 1,
                `mrn` tinyint(1) NOT NULL DEFAULT 1,
                `admit_type` tinyint(1) NOT NULL DEFAULT 1,
                `room` tinyint(1) NOT NULL DEFAULT 1,
                `co_surgeon` tinyint(1) NOT NULL DEFAULT 1,
                `supervising_surgeon` tinyint(1) NOT NULL DEFAULT 1,
                `first_assistant_surgeon` tinyint(1) NOT NULL DEFAULT 1,
                `assistant` tinyint(1) NOT NULL DEFAULT 1,
                `anesthesiologist` tinyint(1) NOT NULL DEFAULT 1,
                `dictated_by` tinyint(1) NOT NULL DEFAULT 1,
                `procedure_id` tinyint(1) NOT NULL DEFAULT 1,
                `pre_op_diagnosis` tinyint(1) NOT NULL DEFAULT 1,
                `operation_time` tinyint(1) NOT NULL DEFAULT 1,
                `post_op_diagnosis` tinyint(1) NOT NULL DEFAULT 1,
                `specimens_removed` tinyint(1) NOT NULL DEFAULT 1,
                `anesthesia_administered` tinyint(1) NOT NULL DEFAULT 1,
                `ebl` tinyint(1) NOT NULL DEFAULT 1,
                `blood_transfused` tinyint(1) NOT NULL DEFAULT 1,
                `fluids` tinyint(1) NOT NULL DEFAULT 1,
                `drains` tinyint(1) NOT NULL DEFAULT 1,
                `urine_output` tinyint(1) NOT NULL DEFAULT 1,
                `total_tourniquet_time` tinyint(1) NOT NULL DEFAULT 1,
                `consent` tinyint(1) NOT NULL DEFAULT 1,
                `complications` tinyint(1) NOT NULL DEFAULT 1,
                `clinical_history` tinyint(1) NOT NULL DEFAULT 1,
                `approach` tinyint(1) NOT NULL DEFAULT 1,
                `findings` tinyint(1) NOT NULL DEFAULT 1,
                `description_procedure` tinyint(1) NOT NULL DEFAULT 1,
                `follow_up_care` tinyint(1) NOT NULL DEFAULT 1,
                `conditions_for_discharge` tinyint(1) NOT NULL DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
            ALTER TABLE `case_op_report_fields_template` ADD PRIMARY KEY (`id`);
            ALTER TABLE `case_op_report_fields_template` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");
	}
}
