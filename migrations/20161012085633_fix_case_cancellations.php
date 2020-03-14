<?php

use \Console\Migration\BaseMigration;

class FixCaseCancellations extends BaseMigration
{
    public function change()
    {
        $this->query("
            UPDATE `case_cancellation`
                INNER JOIN `case` ON `case`.`id` = `case_cancellation`.`case_id` 
                SET `case_cancellation`.`dos` = `case`.`time_start` 
                WHERE `cancel_time` > '2016-09-24 00:00:00';
        ");
    }
}
