<?php

use \Console\Migration\BaseMigration;

class AppointmentDefaultValue extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `case`
                CHANGE COLUMN `appointment_status` `appointment_status` TINYINT(4) NOT NULL DEFAULT '0' AFTER `started_at`;
        ");
	}
}
