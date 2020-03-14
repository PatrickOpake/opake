<?php

use \Console\Migration\BaseMigration;

class CaseStateField extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `case` ADD `state` VARCHAR(255) NULL AFTER `phase`;
        ");
	}
}
