<?php

use \Console\Migration\BaseMigration;

class RemoteFileColumns extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `forms_document`
                ADD COLUMN `remote_file_id` INT(11) NULL DEFAULT NULL AFTER `uploaded_file_id`;
        ");

        $this->query("
             ALTER TABLE `case_registration_documents`
                ADD COLUMN `remote_file_id` INT(11) NULL DEFAULT NULL AFTER `uploaded_file_id`;
        ");


        $this->query("
            CREATE TABLE `case_registration_documents_print_results` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NULL DEFAULT NULL,
                `uploaded_file_id` INT(10) NULL DEFAULT NULL,
                `temp_file_path` VARCHAR(1024) NULL DEFAULT NULL,
                `is_tmp` TINYINT(1) NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");
    }
}
