<?php

use \Console\Migration\BaseMigration;

class DocumentPrintResult extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `case_registration_documents_print_results`
	          RENAME TO `documents_print_results`;
        ");
    }
}
