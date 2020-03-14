<?php

use \Console\Migration\BaseMigration;

class AddFieldsInCaseTypeTable extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `case_type` 
            ADD COLUMN `organization_id` int(11) NOT NULL AFTER `id`,
            ADD KEY `organization_id` (`organization_id`);
            ");
	}
}
