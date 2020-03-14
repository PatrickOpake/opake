<?php

use \Console\Migration\BaseMigration;

class UploadedDate extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `uploaded_files` ADD `uploaded_date` DATETIME NULL DEFAULT NULL;
		");
    }
}
