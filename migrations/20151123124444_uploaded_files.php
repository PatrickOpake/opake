<?php

use \Console\Migration\BaseMigration;

class UploadedFiles extends BaseMigration
{
	public function change()
	{
		$this->query("
            CREATE TABLE `uploaded_files` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `original_filename` VARCHAR(512) NULL DEFAULT NULL,
                `path` VARCHAR(2048) NULL DEFAULT NULL,
                `name` VARCHAR(255) NULL DEFAULT NULL,
                `extension` VARCHAR(50) NULL DEFAULT NULL,
                `mime_type` VARCHAR(50) NULL DEFAULT NULL,
                `system` TINYINT(4) NOT NULL DEFAULT '0',
                `assigned` TINYINT(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");

		$this->query("
             CREATE TABLE `uploaded_files_image_info` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `uploaded_file_id` INT(10) NULL DEFAULT NULL,
                `settings_type` VARCHAR(2048) NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");
	}
}
