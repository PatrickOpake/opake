<?php

use \Console\Migration\BaseMigration;

class ProtectedFiles extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `uploaded_files`
                ADD COLUMN `protected` TINYINT(4) NOT NULL DEFAULT '0';
            ALTER TABLE `uploaded_files`
                    ADD COLUMN `protected_type` VARCHAR(50) NULL DEFAULT NULL;
        ");
	}
}
