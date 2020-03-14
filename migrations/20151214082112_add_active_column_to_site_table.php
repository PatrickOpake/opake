<?php

use \Console\Migration\BaseMigration;

class AddActiveColumnToSiteTable extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `site` ADD COLUMN `active` tinyint(1) NOT NULL DEFAULT 1;
        ");
	}
}
