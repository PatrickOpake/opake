<?php

use \Console\Migration\BaseMigration;

class OverviewDisplaySettings extends BaseMigration
{
    public function change()
    {
        $this->query("
                CREATE TABLE IF NOT EXISTS `room_display_settings` (
                    `id` int(11) NOT NULL,
                    `location_id` int(11) NOT NULL,
                    `overview_position` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ALTER TABLE `room_display_settings` ADD PRIMARY KEY (`id`);
                ALTER TABLE `room_display_settings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
                
                CREATE TABLE IF NOT EXISTS `surgeon_display_settings` (
                    `id` int(11) NOT NULL,
                    `user_id` int(11) NOT NULL,
                    `overview_position` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ALTER TABLE `surgeon_display_settings` ADD PRIMARY KEY (`id`);
                ALTER TABLE `surgeon_display_settings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		");
    }
}
