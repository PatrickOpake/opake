<?php

use \Console\Migration\BaseMigration;

class InsurancePayorAddress extends BaseMigration
{
    public function change()
    {
		$this->query("
			CREATE TABLE `insurance_payor_address` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `payor_id` int(11) DEFAULT NULL,
			  `address` varchar(2048) DEFAULT NULL,
			  `state_id` int(10) unsigned DEFAULT NULL,
			  `city_id` int(10) unsigned DEFAULT NULL,
			  `zip_code` varchar(20) DEFAULT NULL,
			  `phone` varchar(40) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");

	    $this->query("
		  	ALTER TABLE `insurance_data_regular` ADD `selected_insurance_address_id` INT  UNSIGNED  NULL  DEFAULT NULL  AFTER `eligibility_payer_id`;
	    ");

	    $db = $this->getDb();
	    $payorsData = $db->query('select')
		    ->table('insurance_payor')
		    ->fields('id', 'address', 'state_id', 'city_id', 'phone', 'zip_code')
		    ->where('actual', 1)
		    ->execute();

	    foreach ($payorsData as $payorData) {
		    if ($payorData->address || $payorData->state_id || $payorData->city_id || $payorData->phone || $payorData->zip_code) {
			    $db->query('insert')
				    ->table('insurance_payor_address')
				    ->data([
					    'payor_id' => $payorData->id,
					    'address' => $payorData->address,
				        'state_id' => $payorData->state_id,
				        'city_id' => $payorData->city_id,
				        'phone' => $payorData->phone,
				        'zip_code' => $payorData->zip_code
				    ])
				    ->execute();
		    }
	    }
    }
}
