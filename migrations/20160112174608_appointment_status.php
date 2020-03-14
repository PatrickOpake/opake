<?php

use \Console\Migration\BaseMigration;

class AppointmentStatus extends BaseMigration
{
	public function change()
	{
		$this->query("
           ALTER TABLE `case`
              ADD COLUMN `appointment_status` TINYINT NULL DEFAULT '0' AFTER `started_at`;
        ");
	}
}
