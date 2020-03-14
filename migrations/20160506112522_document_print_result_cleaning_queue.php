<?php

use \Console\Migration\BaseMigration;

class DocumentPrintResultCleaningQueue extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `documents_print_results`
                DROP COLUMN `name`,
                DROP COLUMN `temp_file_path`,
                DROP COLUMN `is_tmp`;
        ");

        $this->query("
          CREATE TABLE `documents_print_results_cleaning_queue` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `uploaded_file_id` INT(10) NULL DEFAULT NULL,
                `remote_file_id` INT(10) NULL DEFAULT NULL,
                `is_removed` TINYINT(1) NULL DEFAULT '0',
                `added_date` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");

        $this->query("
            ALTER TABLE `documents_print_results`
	          ADD COLUMN `key` VARCHAR(50) NULL DEFAULT NULL AFTER `uploaded_file_id`;
        ");
    }
}
