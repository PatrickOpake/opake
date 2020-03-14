<?php

use \Console\Migration\BaseMigration;

class UpdateCaseChartTable extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE IF NOT EXISTS `case_booking_list` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `case_id` int(11) NULL DEFAULT NULL,
                    `booking_id` int(11) NULL DEFAULT NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                    
            ALTER TABLE `case_chart` ADD COLUMN `list_id` int(11) NULL DEFAULT NULL AFTER `id`;
        ");

        $rows = $this->getDb()->query('select')
            ->table('case_chart')
            ->execute();

        $this->getDb()->begin_transaction();

        try {
            foreach ($rows as $row) {
                $query = $this->getDb()->query('select')
                    ->table('case_booking_list')
                    ->where(['case_id', $row->case_id])
                    ->execute()
                    ->current();
                
                if (!$query) {
                    $this->getDb()->query('insert')
                        ->table('case_booking_list')
                        ->data(['case_id' => $row->case_id])
                        ->execute();
                }
            }
            $this->getDb()->commit();
        } catch (\Exception $e) {
            $this->getDb()->rollback();
            throw $e;
        }

        $listRows = $this->getDb()->query('select')
            ->table('case_booking_list')
            ->execute();

        $this->getDb()->begin_transaction();

        try {
            foreach ($listRows as $row) {
                $this->getDb()->query('update')
                    ->table('case_chart')
                    ->data(['list_id' => $row->id])
                    ->where(['case_id', $row->case_id])
                    ->execute();
            }
            $this->getDb()->commit();
        } catch (\Exception $e) {
            $this->getDb()->rollback();
            throw $e;
        }

        $this->query("
            ALTER TABLE `case_chart` DROP `case_id`;
        ");
    }
}
