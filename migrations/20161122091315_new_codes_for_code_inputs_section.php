<?php

use \Console\Migration\BaseMigration;

class NewCodesForCodeInputsSection extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE IF NOT EXISTS `discharge_status_code` (
                `id` int(11) NOT NULL,
                `code` varchar(22) NULL DEFAULT NULL,
                `effective_date` DATE NULL DEFAULT NULL,
                `change_date` DATE NULL DEFAULT NULL,
                `delete_date` DATE NULL DEFAULT NULL,
                `verbiage` varchar(255) NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `discharge_status_code` ADD PRIMARY KEY (`id`);
            ALTER TABLE `discharge_status_code` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            
            CREATE TABLE IF NOT EXISTS `condition_code` (
                `id` int(11) NOT NULL,
                `code` varchar(22) NULL DEFAULT NULL,
                `description` varchar(255) NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `condition_code` ADD PRIMARY KEY (`id`);
            ALTER TABLE `condition_code` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            
            CREATE TABLE IF NOT EXISTS `occurrence_code` (
                `id` int(11) NOT NULL,
                `code` varchar(22) NULL DEFAULT NULL,
                `description` varchar(255) NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `occurrence_code` ADD PRIMARY KEY (`id`);
            ALTER TABLE `occurrence_code` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

        ");
    }
}
