<?php

use \Console\Migration\BaseMigration;

class UserViewState extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `user` 
                DROP `dashboard_group_type`,
                DROP `dashboard_view_type`,
                DROP `schedule_view_type`,
                ADD `view_state` TEXT;
                
            UPDATE `user` SET view_state = 'a:3:{s:15:\"dashboard_group\";s:7:\"surgeon\";s:14:\"dashboard_view\";s:3:\"day\";s:13:\"schedule_view\";s:10:\"agendaWeek\";}';
        ");
    }
}
