<?php

use \Console\Migration\BaseMigration;

class RemoteStorageDocuments extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `remote_storage_document` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `filename` VARCHAR(255) NULL DEFAULT NULL,
                `content_item_id` VARCHAR(255) NULL DEFAULT NULL,
                `asset_id` VARCHAR(255) NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");
    }
}
