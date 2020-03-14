<?php

use \Console\Migration\BaseMigration;

class UpdateCaseStructure extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE IF NOT EXISTS `case_pre_op_required_data` (
                `pre_op_required` int(11) NOT NULL,
                `case_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_pre_op_required_data` ADD UNIQUE KEY `uni` (`pre_op_required`,`case_id`);
            
             CREATE TABLE IF NOT EXISTS `case_studies_ordered` (
                `studies_order` int(11) NOT NULL,
                `case_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_studies_ordered` ADD UNIQUE KEY `uni` (`studies_order`,`case_id`);
            
        ");

        $rows = $this->getDb()->query('select')
            ->table('case')
            ->execute();

        $this->getDb()->begin_transaction();

        try {
            foreach ($rows as $row) {
                if ($row->pre_op_none) {
                    $this->getDb()->query('insert')
                        ->table('case_pre_op_required_data')
                        ->data(['case_id' => $row->id, 'pre_op_required' => 0])
                        ->execute();
                }
                if ($row->pre_op_medical_clearance) {
                    $this->getDb()->query('insert')
                        ->table('case_pre_op_required_data')
                        ->data(['case_id' => $row->id, 'pre_op_required' => 1])
                        ->execute();
                }
                if ($row->pre_op_labs) {
                    $this->getDb()->query('insert')
                        ->table('case_pre_op_required_data')
                        ->data(['case_id' => $row->id, 'pre_op_required' => 2])
                        ->execute();
                }
                if ($row->pre_op_xray) {
                    $this->getDb()->query('insert')
                        ->table('case_pre_op_required_data')
                        ->data(['case_id' => $row->id, 'pre_op_required' => 3])
                        ->execute();
                }
                if ($row->pre_op_ekg) {
                    $this->getDb()->query('insert')
                        ->table('case_pre_op_required_data')
                        ->data(['case_id' => $row->id, 'pre_op_required' => 4])
                        ->execute();
                }

                if ($row->studies_ordered_none) {
                    $this->getDb()->query('insert')
                        ->table('case_studies_ordered')
                        ->data(['case_id' => $row->id, 'studies_order' => 0])
                        ->execute();
                }
                if ($row->studies_ordered_cbc) {
                    $this->getDb()->query('insert')
                        ->table('case_studies_ordered')
                        ->data(['case_id' => $row->id, 'studies_order' => 1])
                        ->execute();
                }
                if ($row->studies_ordered_chems) {
                    $this->getDb()->query('insert')
                        ->table('case_studies_ordered')
                        ->data(['case_id' => $row->id, 'studies_order' => 2])
                        ->execute();
                }
                if ($row->studies_ordered_ekg) {
                    $this->getDb()->query('insert')
                        ->table('case_studies_ordered')
                        ->data(['case_id' => $row->id, 'studies_order' => 3])
                        ->execute();
                }
                if ($row->studies_ordered_pt_pit) {
                    $this->getDb()->query('insert')
                        ->table('case_studies_ordered')
                        ->data(['case_id' => $row->id, 'studies_order' => 4])
                        ->execute();
                }
                if ($row->studies_ordered_cxr) {
                    $this->getDb()->query('insert')
                        ->table('case_studies_ordered')
                        ->data(['case_id' => $row->id, 'studies_order' => 5])
                        ->execute();
                }
                if ($row->studies_ordered_lft) {
                    $this->getDb()->query('insert')
                        ->table('case_studies_ordered')
                        ->data(['case_id' => $row->id, 'studies_order' => 6])
                        ->execute();
                }
                if ($row->studies_ordered_dig_level) {
                    $this->getDb()->query('insert')
                        ->table('case_studies_ordered')
                        ->data(['case_id' => $row->id, 'studies_order' => 7])
                        ->execute();
                }
                if ($row->studies_ordered_other) {
                    $this->getDb()->query('insert')
                        ->table('case_studies_ordered')
                        ->data(['case_id' => $row->id, 'studies_order' => 9])
                        ->execute();
                }
            }

            $this->query("
            ALTER TABLE `case` DROP `pre_op_none`,
                DROP `pre_op_medical_clearance`,
                DROP `pre_op_labs`,
                DROP `pre_op_xray`,
                DROP `pre_op_ekg`,
                DROP `studies_ordered_none`,
                DROP `studies_ordered_cbc`,
                DROP `studies_ordered_chems`,
                DROP `studies_ordered_ekg`,
                DROP `studies_ordered_pt_pit`,
                DROP `studies_ordered_cxr`,
                DROP `studies_ordered_lft`,
                DROP `studies_ordered_dig_level`,
                DROP `studies_ordered_other`;
            ");

            $this->getDb()->commit();
        } catch (\Exception $e) {
            $this->getDb()->rollback();
            throw $e;
        }
    }
}
