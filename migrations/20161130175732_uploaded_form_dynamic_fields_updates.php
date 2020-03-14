<?php

use \Console\Migration\BaseMigration;

class UploadedFormDynamicFieldsUpdates extends BaseMigration
{
    public function change()
    {
        $this->query("
	    ALTER TABLE `forms_document_pdf_dynamic_field` CHANGE `field` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
	    ALTER TABLE `forms_document_pdf_dynamic_field` ADD INDEX `IDX_doc_id` (`doc_id`);
        ");
    }
}
