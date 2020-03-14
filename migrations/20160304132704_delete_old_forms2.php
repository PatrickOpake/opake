<?php

use \Console\Migration\BaseMigration;

class DeleteOldForms2 extends BaseMigration
{
	public function change()
	{
		$this->query("
            DELETE
            FROM case_registration_documents
            WHERE document_type IS NULL;

            DELETE
            FROM forms_document
            WHERE segment='intake' AND doc_type_id IS NULL;
        ");
	}
}
