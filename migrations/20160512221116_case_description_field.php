<?php

use \Console\Migration\BaseMigration;

class CaseDescriptionField extends BaseMigration
{

	public function change()
	{
		$this->query("ALTER TABLE `case` ADD `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL AFTER `location_id`;");
	}

}
