<?php

use \Console\Migration\BaseMigration;

class FormDocumentAllFields extends BaseMigration
{
	public function change()
	{
		$this->query("
            ALTER TABLE `forms_document`
                ADD COLUMN `is_all_sites` TINYINT NOT NULL DEFAULT '0' AFTER `doc_type_id`,
                ADD COLUMN `is_all_case_types` TINYINT NOT NULL DEFAULT '0' AFTER `is_all_sites`;
        ");
	}
}
