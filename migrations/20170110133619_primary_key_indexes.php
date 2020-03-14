<?php

use \Console\Migration\BaseMigration;

class PrimaryKeyIndexes extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `case_additional_type` DROP INDEX `uni`;
			ALTER TABLE `case_additional_type` ADD PRIMARY KEY (`type_id`, `case_id`);
		");

	    $this->query("
			ALTER TABLE `case_anesthesiologist` DROP INDEX `uni`;
			ALTER TABLE `case_anesthesiologist` ADD PRIMARY KEY (`anesthesiologist_id`, `case_id`);
		");


	    $this->query("
			ALTER TABLE `case_assistant` DROP INDEX `uni`;
			ALTER TABLE `case_assistant` ADD PRIMARY KEY (`assistant_id`, `case_id`);
		");

	    $this->query("
			ALTER TABLE `case_co_surgeon` DROP INDEX `uni`;
			ALTER TABLE `case_co_surgeon` ADD PRIMARY KEY (`co_surgeon_id`, `case_id`);
		");

	    $this->query("
			ALTER TABLE `case_cpt` DROP INDEX `uni`;
			ALTER TABLE `case_cpt` DROP INDEX `case_id`;
			ALTER TABLE `case_cpt` DROP INDEX `cpt_id`;
			ALTER TABLE `case_cpt` ADD PRIMARY KEY (`case_id`, `cpt_id`);
		");


	    $this->query("
			ALTER TABLE `case_dictated_by` DROP INDEX `uni`;
			ALTER TABLE `case_dictated_by` ADD PRIMARY KEY (`dictated_by_id`, `case_id`);
		");


	    $this->query("
			ALTER TABLE `case_equipment` DROP INDEX `uni`;
			ALTER TABLE `case_equipment` ADD PRIMARY KEY (`inventory_id`, `case_id`);
		");


	    $this->query("
			ALTER TABLE `case_first_assistant_surgeon` DROP INDEX `uni`;
			ALTER TABLE `case_first_assistant_surgeon` ADD PRIMARY KEY (`assistant_surgeon_id`, `case_id`);
		");

	    $this->query("
			ALTER TABLE `case_implant` DROP INDEX `uni`;
			ALTER TABLE `case_implant` ADD PRIMARY KEY (`inventory_id`, `case_id`);
		");

	    $this->query("
			ALTER TABLE `case_op_report_future_user` DROP INDEX `uni`;
			ALTER TABLE `case_op_report_future_user` ADD PRIMARY KEY (`report_id`, `user_id`);
		");


	    $this->query("
			ALTER TABLE `case_other_staff` ADD PRIMARY KEY (`staff_id`, `case_id`);
		");

	    $this->query("
			ALTER TABLE `case_pre_op_required_data` DROP INDEX `uni`;
			ALTER TABLE `case_pre_op_required_data` ADD PRIMARY KEY (`pre_op_required`, `case_id`);
		");

	    $this->query("
			ALTER TABLE `case_studies_ordered` DROP INDEX `uni`;
			ALTER TABLE `case_studies_ordered` ADD PRIMARY KEY (`studies_order`, `case_id`);
		");

	    $this->query("
			ALTER TABLE `case_supervising_surgeon` DROP INDEX `uni`;
			ALTER TABLE `case_supervising_surgeon` ADD PRIMARY KEY (`supervising_surgeon_id`, `case_id`);
		");

	    $this->query("
			ALTER TABLE `case_surgeon_assistant` DROP INDEX `uni`;
			ALTER TABLE `case_surgeon_assistant` ADD PRIMARY KEY (`surgeon_assistant_id`, `case_id`);
		");

	    $this->query("
			ALTER TABLE `case_time_log_staff` DROP INDEX `uni`;
			ALTER TABLE `case_time_log_staff` ADD PRIMARY KEY (`timelog_id`, `staff_id`);
		");

	    $this->query("
			ALTER TABLE `case_type_cpt` DROP INDEX `uni`;
			ALTER TABLE `case_type_cpt` DROP INDEX `case_type_id`;
			ALTER TABLE `case_type_cpt` DROP INDEX `cpt_id`;
			ALTER TABLE `case_type_cpt` ADD PRIMARY KEY (`case_type_id`, `cpt_id`);
		");

	    $this->query("
			ALTER TABLE `case_user` DROP INDEX `uni`;
			ALTER TABLE `case_user` ADD PRIMARY KEY (`case_id`, `user_id`);
		");

	    $this->query("
			ALTER TABLE `booking_additional_type` DROP INDEX `uni`;
			ALTER TABLE `booking_additional_type` ADD PRIMARY KEY (`type_id`, `booking_id`);
		");

	    $this->query("
			ALTER TABLE `booking_assistant` DROP INDEX `uni`;
			ALTER TABLE `booking_assistant` ADD PRIMARY KEY (`assistant_id`, `booking_id`);
		");

	    $this->query("
			ALTER TABLE `booking_equipment` DROP INDEX `uni`;
			ALTER TABLE `booking_equipment` ADD PRIMARY KEY (`inventory_id`, `booking_id`);
		");

	    $this->query("
			ALTER TABLE `booking_implant` DROP INDEX `uni`;
			ALTER TABLE `booking_implant` ADD PRIMARY KEY (`inventory_id`, `booking_id`);
		");

	    $this->query("
			ALTER TABLE `booking_other_staff` DROP INDEX `uni`;
			ALTER TABLE `booking_other_staff` ADD PRIMARY KEY (`staff_id`, `booking_id`);
		");

	    $this->query("
			ALTER TABLE `booking_studies_ordered` DROP INDEX `uni`;
			ALTER TABLE `booking_studies_ordered` ADD PRIMARY KEY (`studies_order`, `booking_id`);
		");

	    $this->query("
			ALTER TABLE `booking_user` ADD PRIMARY KEY (`booking_id`, `user_id`);
		");

	    $this->query("
			ALTER TABLE `coding_condition_code` DROP INDEX `uni`;
			ALTER TABLE `coding_condition_code` ADD PRIMARY KEY (`coding_id`, `code_id`);
		");

	    $this->query("
			ALTER TABLE `forms_document_case_type` DROP INDEX `uni`;
			ALTER TABLE `forms_document_case_type` ADD PRIMARY KEY (`doc_id`, `case_type_id`);
		");

	    $this->query("
			ALTER TABLE `forms_document_site` DROP INDEX `uni`;
			ALTER TABLE `forms_document_site` ADD PRIMARY KEY (`doc_id`, `site_id`);
		");

	    $this->query("
			ALTER TABLE `pref_card_staff_case_type` DROP INDEX `uni`;
			ALTER TABLE `pref_card_staff_case_type` ADD PRIMARY KEY (`pref_card_staff_id`, `type_id`);
		");
    }
}
