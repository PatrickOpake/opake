<?php

use \Console\Migration\BaseMigration;

class InsurancesOrder extends BaseMigration
{
    public function change()
    {
        $db = $this->getDb();
        $db->begin_transaction();
        try {

            $this->updateTable('case_registration_insurance_types', 'id', 'registration_id', 'order');
            $this->updateTable('booking_insurance_types', 'id', 'booking_id', 'order');

            $db->commit();
        } catch (\Exception $e) {
            $db->rollback();
            throw $e;
        }
    }

    protected function updateTable($table, $idColumn, $objectIdColumn, $orderColumn)
    {
        $db = $this->getDb();
        $orderedRows = $db->query('select')
            ->table($table)
            ->fields($idColumn, $objectIdColumn, $orderColumn)
            ->where($orderColumn, 'IS NOT NULL', $db->expr(''))
            ->execute()->as_array();

        $notOrderedRows =  $db->query('select')
            ->table($table)
            ->fields($idColumn, $objectIdColumn, $orderColumn)
            ->where('and', [
                ['or', [$orderColumn, 'IS NULL', $db->expr('')]],
                ['or', [$orderColumn, 0]],
            ])
            ->execute()->as_array();

        $orderedInsurances = [];
        foreach ($orderedRows as $row) {
            $objectId = $row->$objectIdColumn;
            $order = $row->$orderColumn;
            $id = $row->$idColumn;
            if ($objectId) {
                if (!isset($orderedInsurances[$objectId][$order])) {
                    $orderedInsurances[$objectId][$order] = $id;
                } else {
                    $i = 1;
                    while (true) {
                        if (!isset($orderedInsurances[$objectId][$i])) {
                            $orderedInsurances[$objectId][$i] = $id;
                            break;
                        }
                        ++$i;
                    }
                }
            }
        }

        foreach ($notOrderedRows as $row) {
            $objectId = $row->$objectIdColumn;
            $order = $row->$orderColumn;
            $id = $row->$idColumn;
            if ($objectId) {
                $i = 1;
                while (true) {
                    if (!isset($orderedInsurances[$objectId][$i])) {
                        $orderedInsurances[$objectId][$i] = $id;
                        break;
                    }
                    ++$i;
                }
            }
        }

        foreach ($orderedInsurances as $objectId => $records) {
            foreach ($records as $order => $rowId) {

                if ($order > 4) {
                    $order = 4;
                }

                $db->query('update')
                    ->table($table)
                    ->data([
                        $orderColumn => $order
                    ])
                    ->where($idColumn, $rowId)
                    ->execute();
            }
        }

    }
}
