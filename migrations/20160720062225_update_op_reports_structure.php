<?php

use \Console\Migration\BaseMigration;

class UpdateOpReportsStructure extends BaseMigration
{
    public function change()
    {
        $this->query("       
            ALTER TABLE `case_op_report` ADD COLUMN `case_id` int(11) NOT NULL AFTER `id`;
        ");

        $rows = $this->getDb()->query('select')
            ->table('case_case_op_report')
            ->execute();

        $this->getDb()->begin_transaction();

        try {
            foreach ($rows as $row) {
                $this->getDb()->query('update')
                    ->table('case_op_report')
                    ->data(['case_id' => $row->case_id])
                    ->where('id', $row->report_id)
                    ->execute();
            }

            $this->getDb()->commit();
        } catch (\Exception $e) {
            $this->getDb()->rollback();
            throw $e;
        }

        $this->query("
            DROP TABLE `case_case_op_report`;
        ");
    }
}
