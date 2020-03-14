<?php

use \Console\Migration\BaseMigration;

class PreOperativeAdmin extends BaseMigration
{
    public function change()
    {

	    $this->query("
	        CREATE TABLE `patient_appointment_form_pre_operative_admin` (
				  `id` int(10) NOT NULL AUTO_INCREMENT,
				  `case_registration_id` int(10) DEFAULT NULL,
				  `filled_date` datetime DEFAULT NULL,
				  `height_ft` int(11) DEFAULT NULL,
				  `height_in` int(11) DEFAULT NULL,
				  `weight_lbs` int(11) DEFAULT NULL,
				  `smoke_how_long_yrs` int(11) DEFAULT NULL,
				  `smoke_packs_per_day` int(11) DEFAULT NULL,
				  `drink_how_long_yrs` int(11) DEFAULT NULL,
				  `drink_drinks_per_week` int(11) DEFAULT NULL,
				  `medications` mediumtext,
				  `steroids` mediumtext,
				  `allergies` mediumtext,
				  `surgeries_hospitalizations` mediumtext,
				  `family_problems` mediumtext,
				  `family_anesthesia_problems` mediumtext,
				  `conditions` mediumtext,
				  `travel_outside` mediumtext,
				  `allergic_to_latex` tinyint(1) DEFAULT NULL,
				  `allergic_to_food` tinyint(1) DEFAULT NULL,
				  `allergic_other` tinyint(1) DEFAULT NULL,
				  `smoke` tinyint(1) DEFAULT NULL,
				  `drink` tinyint(1) DEFAULT NULL,
				  `living_will` tinyint(1) DEFAULT NULL,
				  `leave_message` tinyint(1) DEFAULT NULL,
				  `allergic_to_latex_reason` varchar(255) DEFAULT NULL,
				  `allergic_to_food_reason` varchar(255) DEFAULT NULL,
				  `allergic_other_reason` varchar(255) DEFAULT NULL,
				  `drink_description` varchar(255) DEFAULT NULL,
				  `primary_care_name` varchar(255) DEFAULT NULL,
				  `caretaker_name` varchar(255) DEFAULT NULL,
				  `transportation_name` varchar(255) DEFAULT NULL,
				  `smoke_description` varchar(255) DEFAULT NULL,
				  `primary_care_phone` varchar(20) DEFAULT NULL,
				  `caretaker_phone` varchar(20) DEFAULT NULL,
				  `leave_message_phone` varchar(20) DEFAULT NULL,
				  `transportation_phone` varchar(20) DEFAULT NULL,
				  `confirmed_patient_demographics` tinyint(1) DEFAULT NULL,
				  `correction_made` tinyint(1) DEFAULT NULL,
				  `history_of_present_illness` varchar(255) DEFAULT NULL,
				  `illicit_drugs` tinyint(1) DEFAULT NULL,
				  `communicable_diseases` mediumtext,
				  `cultural_limitations` mediumtext,
				  `pain_management` mediumtext,
				  `illicit_drugs_description` varchar(255) DEFAULT NULL,
				  `illicit_drugs_how_long_yrs` int(11) DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  KEY `IDX_case_registration_id` (`case_registration_id`)
				) ENGINE=InnoDB;
	    ");
    }
}
