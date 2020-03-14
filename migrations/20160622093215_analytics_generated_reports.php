<?php

use \Console\Migration\BaseMigration;

class AnalyticsGeneratedReports extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `analytics_generated_reports` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `file_id` INT(10) NULL,
                `key` VARCHAR(50) NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");
    }
}
