<?php

use \Console\Migration\BaseMigration;

class FormsDocumentToCaseType extends BaseMigration
{
	public function change()
	{
		$this->query("
            CREATE TABLE IF NOT EXISTS `forms_document_case_type` (
                `doc_id` int(11) NOT NULL,
                `case_type_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `forms_document_case_type`
                ADD UNIQUE KEY `uni` (`doc_id`,`case_type_id`);
        ");
	}
}
