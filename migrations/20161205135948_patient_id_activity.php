<?php

use \Console\Migration\BaseMigration;
use Opake\Model\Analytics\UserActivity\ActivityRecord;

class PatientIdActivity extends BaseMigration
{
    public function change()
    {
	    $this->query("ALTER TABLE `user_activity_search_params` ADD `patient_id` INT(11) NULL DEFAULT NULL");

		$db = $this->getDb();
	    $rows = $db->query('select')
		    ->table('user_activity')
		    ->fields('id', 'details')
		    ->where('action', 'IN', $db->arr([
			    ActivityRecord::ACTION_INTAKE_EDIT_PATIENT_DETAILS,
			    ActivityRecord::ACTION_PATIENT_CREATE,
			    ActivityRecord::ACTION_PATIENT_EDIT,
			    ActivityRecord::ACTION_PATIENT_ADD_INSURANCE,
			    ActivityRecord::ACTION_PATIENT_EDIT_INSURANCE,
			    ActivityRecord::ACTION_PATIENT_REMOVE_INSURANCE
		    ]))
		    ->execute();

	    $db->begin_transaction();
	    try {
		    foreach ($rows as $row) {
			    if ($row->details) {
				    $details = unserialize($row->details);
				    if (isset($details['patient'])) {
					    $patientId = $details['patient'];
						$db->query('insert')
							->table('user_activity_search_params')
							->data([
								'user_activity_id' => $row->id,
							    'patient_id' => $patientId
							])->execute();
				    }
			    }
	        }

		    $db->commit();

	    } catch (\Exception $e) {
		    $db->rollback();
		    throw $e;
	    }
    }
}
