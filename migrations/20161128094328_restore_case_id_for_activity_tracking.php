<?php

use \Console\Migration\BaseMigration;

class RestoreCaseIdForActivityTracking extends BaseMigration
{

    public function change()
    {
		$db = $this->getDb();

	    $db->begin_transaction();

	    try {
		    $rows = $db->query('select')
			    ->table('user_activity')
			    ->fields('id', 'details')
			    ->where('action', 'IN', $db->arr([
				    \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_ADD_CHECKLIST_ITEM,
				    \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_EDIT_CHECKLIST_ITEM,
				    \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_REMOVE_CHECKLIST_ITEM,
				    \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_BOOKING_SCHEDULE,
			    ]))
			    ->execute();

		    foreach ($rows as $row) {
				if ($row->details) {

					try {
						$details = unserialize($row->details);
					} catch (\Exception $e) {
						print "Error while unserialization: " . $e->getMessage() . "\r\n";
						continue;
					}

					if (isset($details['case'])) {
						$db->query('insert')
							->table('user_activity_search_params')
							->data([
								'user_activity_id' => $row->id,
								'case_id' => $details['case']
							])
							->execute();
					}
					if (isset($details['case_id'])) {
						$db->query('insert')
							->table('user_activity_search_params')
							->data([
								'user_activity_id' => $row->id,
								'case_id' => $details['case_id']
							])
							->execute();
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
