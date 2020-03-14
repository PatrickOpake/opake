<?php

use \Console\Migration\BaseMigration;

class UpdateOpReports extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE IF NOT EXISTS `case_case_op_report` (
                    `case_id` int(11) NOT NULL,
                    `report_id` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_case_op_report` ADD UNIQUE KEY `uni` (`case_id`,`report_id`);
                    
            ALTER TABLE `case_op_report` ADD COLUMN `type` TINYINT NOT NULL DEFAULT 0 AFTER `id`;
        ");

        $rows = $this->getDb()->query('select')
            ->table('case_op_report')
            ->execute();

        $this->getDb()->begin_transaction();

        try {
            foreach ($rows as $row) {
                $this->getDb()->query('insert')
                    ->table('case_case_op_report')
                    ->data([
                        'case_id' => $row->case_id,
                        'report_id' => $row->id
                    ])->execute();
            }

            $this->getDb()->commit();
        } catch (\Exception $e) {
            $this->getDb()->rollback();
            throw $e;
        }

        $this->query("
            ALTER TABLE `case_op_report` DROP `case_id`;
        ");

    }
}
