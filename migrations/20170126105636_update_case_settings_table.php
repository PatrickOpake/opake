<?php

use \Console\Migration\BaseMigration;

class UpdateCaseSettingsTable extends BaseMigration
{
    public function change()
    {
        $this->query('
            ALTER TABLE `case_setting` ADD COLUMN `display_timestamp_on_printout` tinyint(1) NULL DEFAULT 0;
        ');
    }
}
