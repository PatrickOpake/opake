<?php

use \Console\Migration\BaseMigration;

class UploadedFormDynamicFields extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `forms_document_pdf_dynamic_field` (
                `id` INT(11) AUTO_INCREMENT,
                `doc_id` INT(11) NOT NULL,
                `page` SMALLINT NOT NULL,
                `field` VARCHAR(255) NOT NULL,
                `x` FLOAT NOT NULL,
                `y` FLOAT NOT NULL,
                `width` FLOAT NOT NULL,
                `height` FLOAT NOT NULL,
                PRIMARY KEY (`id`)
	    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

	    ALTER TABLE `forms_document_pdf_dynamic_field` ADD INDEX `IDX_document_id` (`document_id`);
        ");
    }
}
