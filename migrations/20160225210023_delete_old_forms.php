<?php

use \Console\Migration\BaseMigration;

class DeleteOldForms extends BaseMigration
{
	public function change()
	{
		$this->query("
            DELETE
            FROM case_registration_document_types
            WHERE is_required = 0 AND id NOT IN (
                SELECT DISTINCT doc_type_id
                FROM forms_document
                WHERE doc_type_id IS NOT NULL)
        ");
	}
}
