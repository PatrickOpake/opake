<?php

use \Console\Migration\BaseMigration;

class AddNameToOpReportTemplate extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `case_op_report` ADD COLUMN `name` VARCHAR(255) NULL AFTER `id`;
            UPDATE `case_op_report` SET name = CONCAT('Template ', id);
            ALTER TABLE `case_op_report_future` ADD COLUMN `name` VARCHAR(255) NULL AFTER `id`;
            UPDATE `case_op_report_future` SET name = CONCAT('Template ', id);
            ALTER TABLE `case` ADD COLUMN `report_id` INT(11) NULL DEFAULT NULL AFTER `id`;
        ");
	}
}
