<?php

use \Console\Migration\BaseMigration;

class CaseTimeLogUpdates extends BaseMigration
{
    public function change()
    {
	    // 1. remove no-actual data - we did't have cascade delete before
	    // 2. use text keys for time-log
	    // 3. use normal time
	    $this->query("
		    DELETE `case_time_log`, `case_time_log_staff`
			FROM `case_time_log`
			LEFT JOIN `case_time_log_staff` ON `case_time_log`.`id` = `case_time_log_staff`.`timelog_id`
			LEFT JOIN `case` ON `case_time_log`.`case_id` = `case`.`id`
			WHERE `case`.`id` IS NULL;

		    UPDATE `case_time_log` SET `stage`='facility_arrival' WHERE `stage`='0';
		    UPDATE `case_time_log` SET `stage`='pre_op_arrival' WHERE `stage`='1';
		    UPDATE `case_time_log` SET `stage`='pre_op_exit' WHERE `stage`='2';
		    UPDATE `case_time_log` SET `stage`='enter_or' WHERE `stage`='3';
		    UPDATE `case_time_log` SET `stage`='anesthesia_start' WHERE `stage`='4';
		    UPDATE `case_time_log` SET `stage`='incision' WHERE `stage`='5';
		    UPDATE `case_time_log` SET `stage`='closure' WHERE `stage`='6';
		    UPDATE `case_time_log` SET `stage`='anesthesia_finish' WHERE `stage`='7';
		    UPDATE `case_time_log` SET `stage`='operation_room_exit' WHERE `stage`='8';
		    UPDATE `case_time_log` SET `stage`='post_op_exit' WHERE `stage`='9';
		    UPDATE `case_time_log` SET `stage`='facility_discharge' WHERE `stage`='10';

		    UPDATE `case_time_log` SET `time` = SUBTIME(`time`, '12:00:00') WHERE `time`IS NOT NULL AND `time`>='12:00:00' AND `time_mode`='AM';
		    UPDATE `case_time_log` SET `time` = ADDTIME(`time`, '12:00:00') WHERE `time`IS NOT NULL AND `time`<'12:00:00' AND `time_mode`='PM';
		    ALTER TABLE `case_time_log` DROP COLUMN `time_mode`;
	    ");
    }
}
