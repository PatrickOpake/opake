<?php

use \Console\Migration\BaseMigration;

class CaseCancelAttempts extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE IF NOT EXISTS `case_cancel_attempt` (
                `id` int(11) NOT NULL,
                `case_id` int(11) NOT NULL,
                `date_called` DATE NULL DEFAULT NULL,
                `initials` varchar(11) NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_cancel_attempt` ADD PRIMARY KEY (`id`);
            ALTER TABLE `case_cancel_attempt` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");
    }
}
