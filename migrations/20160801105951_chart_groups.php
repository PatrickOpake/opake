<?php

use \Console\Migration\BaseMigration;

class ChartGroups extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `forms_chart_group` (
                `id` INT(10) NOT NULL AUTO_INCREMENT,
                `organization_id` INT(10) NULL DEFAULT NULL,
                `name` VARCHAR(255) NULL,
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");

        $this->query("
            CREATE TABLE `forms_chart_group_document` (
                `chart_group_id` INT(10) NULL,
                `form_document_id` INT(10) NULL,
                PRIMARY KEY (`chart_group_id`, `form_document_id`)
            )
            ENGINE=InnoDB;
        ");
    }
}
