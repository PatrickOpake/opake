<?php

use \Console\Migration\BaseMigration;

class UpdateCaseBlockingFields extends BaseMigration
{
	public function change()
	{
		$this->query('
			ALTER TABLE `case_blocking` 
                CHANGE `range_from` `date_from` date NULL DEFAULT NULL,
                CHANGE `range_to` `date_to` date NULL DEFAULT NULL,
			    ADD `time_from` time NULL DEFAULT NULL AFTER `date_to`,
                ADD `time_to` time NULL DEFAULT NULL AFTER `time_from`;
        ');
	}
}
