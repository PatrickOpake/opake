<?php

use \Console\Migration\BaseMigration;

class Diagnoses extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `case_coding_bill_diagnosis` (
			  `bill_id` int(11) unsigned NOT NULL DEFAULT '0',
			  `diagnosis_number` int(11) unsigned NOT NULL DEFAULT '0',
			  `order` tinyint(4) unsigned NOT NULL DEFAULT '0',
			  PRIMARY KEY (`bill_id`,`diagnosis_number`)
			) ENGINE=InnoDB;
		");

	    $db = $this->getDb();
	    $db->begin_transaction();
	    try {

		    $billsQuery = $db->query('select')
			    ->table('case_coding_bill')
			    ->where('diagnosis_row', 'IS NOT NULL', $this->getDb()->expr(''))
			    ->execute();

		    foreach ($billsQuery as $row) {
			    $db->query('insert')
				    ->table('case_coding_bill_diagnosis')
				    ->data([
					    'bill_id' => $row->id,
				        'diagnosis_number' => $row->diagnosis_row
				    ])
			        ->execute();
		    }

		    $db->commit();
	    } catch (\Exception $e) {
		    $db->rollback();
	    }
    }
}
