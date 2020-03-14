<?php

use \Console\Migration\BaseMigration;

class QuaternaryInsurance extends BaseMigration
{
    public function change()
    {
	    $db = $this->getDb();
	    try {
		    $db->begin_transaction();

		    $this->correctOrderForTable(
			    'booking_insurance_types',
			    'booking_id'
		    );

		    $this->correctOrderForTable(
			    'case_registration_insurance_types',
			    'registration_id'
		    );

		    $this->correctOrderForTable(
			    'booking_patient_insurance_types',
			    'booking_patient_id'
		    );

		    $this->correctOrderForTable(
			    'patient_insurance_types',
			    'patient_id'
		    );

		    $db->commit();
	    } catch (\Exception $e) {
		    $db->rollback();
		    throw $e;
	    }
    }

	protected function correctOrderForTable($tableName, $objectColumn)
	{
		$db = $this->getDb();
		$rows = $db->query('select')
			->table($tableName)
			->fields('id', $objectColumn)
			->where('order', 4)
			->execute();

		$groups = [];
		foreach ($rows as $row) {
			$objectId = $row->$objectColumn;
			if (!isset($groups[$objectId])) {
				$groups[$objectId] = [];
			}
			$groups[$objectId][] = $row->id;
		}

		foreach ($groups as $rowIds) {
			foreach ($rowIds as $index => $id) {
				if ($index !== 0) {
					$db->query('update')
						->table($tableName)
						->data([
							'order' => 5
						])
						->where('id', $id)
						->execute();
				}
			}
		}
	}
}
