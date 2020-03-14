<?php

use \Console\Migration\BaseMigration;

class FormsLandscapeProperty extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `forms_document`
                ADD COLUMN `is_landscape` TINYINT(1) NULL DEFAULT 0;
        ");
	}
}
