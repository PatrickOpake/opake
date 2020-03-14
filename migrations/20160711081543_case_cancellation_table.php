<?php

use \Console\Migration\BaseMigration;

class CaseCancellationTable extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE IF NOT EXISTS `case_cancellation` (
                `id` int(11) NOT NULL,
                `case_id` int(11) NOT NULL,
                `dos` DATETIME NULL DEFAULT NULL,
                `cancel_time` DATETIME NULL DEFAULT NULL,
                `cancel_status` INT NULL DEFAULT NULL,
                `cancel_reason` VARCHAR (255) NULL DEFAULT NULL,
                `canceled_user_id` INT NULL DEFAULT NULL,
                `rescheduled_date` DATETIME NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_cancellation` ADD PRIMARY KEY (`id`);
            ALTER TABLE `case_cancellation` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            
            ALTER TABLE `case_cancel_attempt` ADD COLUMN `case_cancellation_id` int(11) NOT NULL AFTER `case_id`;
        ");

        $caseRows = $this->getDb()->query('select')
            ->table('case')
            ->where('appointment_status', 1)
            ->execute();

        $this->getDb()->begin_transaction();

        try {
            foreach ($caseRows as $row) {
                $this->getDb()->query('insert')
                    ->table('case_cancellation')
                    ->data([
                        'case_id' => $row->id,
                        'dos' => $row->time_start,
                        'cancel_time' => $row->cancel_time,
                        'cancel_status' => $row->cancel_status,
                        'cancel_reason' => $row->cancel_reason,
                        'canceled_user_id' => $row->canceled_user_id
                    ])->execute();
            }

            $this->getDb()->commit();
        } catch (\Exception $e) {
            $this->getDb()->rollback();
            throw $e;
        }

        $caseCancellationRows = $this->getDb()->query('select')
            ->table('case_cancellation')
            ->execute();

        $this->getDb()->begin_transaction();

        try {
            foreach ($caseCancellationRows as $row) {
                $this->getDb()->query('update')
                    ->table('case_cancel_attempt')
                    ->data(['case_cancellation_id' => $row->id])
                    ->where('case_id', $row->case_id)
                    ->execute();
            }

            $this->getDb()->commit();
        } catch (\Exception $e) {
            $this->getDb()->rollback();
            throw $e;
        }

        $this->query("                  
            ALTER TABLE `case`
                DROP `cancel_time`,
                DROP `cancel_status`,
                DROP `cancel_reason`,
                DROP `canceled_user_id`;
            ALTER TABLE `case_cancel_attempt` DROP `case_id`;
            
            UPDATE `case_cancellation` SET cancel_status = 100 WHERE cancel_status = 5;
            UPDATE `case_cancellation` SET cancel_status = 5 WHERE cancel_status = 4;
            UPDATE `case_cancellation` SET cancel_status = 4 WHERE cancel_status = 100;
        ");
    }
}
