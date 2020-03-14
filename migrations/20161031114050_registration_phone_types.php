<?php

use \Console\Migration\BaseMigration;

class RegistrationPhoneTypes extends BaseMigration
{
    public function change()
    {
	    $db = $this->getDb();
	    $db->begin_transaction();

	    $rows = $db->query('select')
		    ->table('patient')
		    ->fields('id', 'parents_name', 'home_phone_type', 'additional_phone_type', 'ec_phone_type')
		    ->execute();

	    try {
		    foreach ($rows as $row) {

			    $db->query('update')
				    ->table('case_registration')
				    ->data([
					    'parents_name' => $row->parents_name,
				        'home_phone_type' => $row->home_phone_type,
				        'additional_phone_type' => $row->additional_phone_type,
				        'ec_phone_type' => $row->ec_phone_type
				    ])
				    ->where('patient_id', $row->id)
			        ->execute();

		    }

		    $db->commit();

	    } catch (\Exception $e) {
		    $db->rollback();
		    throw $e;
	    }
    }
}
