<?php

use \Console\Migration\BaseMigration;

class CaseStagesPhases extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `case`
		ADD `stage` VARCHAR(30) NULL AFTER `location_id`,
		ADD `phase` VARCHAR(30) NULL AFTER `stage`;
        ");
	}
}
