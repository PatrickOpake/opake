<?php

use \Console\Migration\BaseMigration;

class AddOverviewAndScheduleViewSettings extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `user` 
              ADD `dashboard_view_type` VARCHAR(20) NOT NULL DEFAULT 'day',
              ADD `schedule_view_type` VARCHAR(20) NOT NULL DEFAULT 'agendaWeek';
        ");
    }
}
