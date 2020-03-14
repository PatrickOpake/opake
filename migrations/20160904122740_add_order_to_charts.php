<?php

use \Console\Migration\BaseMigration;

class AddOrderToCharts extends BaseMigration
{
    public function change()
    {
        $this->query("
           ALTER TABLE `forms_chart_group_document`
                ADD COLUMN `order` INT(10) NOT NULL DEFAULT '0' AFTER `form_document_id`;
        ");
    }
}
