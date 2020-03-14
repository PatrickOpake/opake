<?php

use \Console\Migration\BaseMigration;

class PrefCardsUpdates extends BaseMigration
{
	public function change()
	{
		$this->query("
		DELETE pref_card_staff, pref_card_staff_item, pref_card_staff_note FROM pref_card_staff
			LEFT JOIN case_type ON pref_card_staff.case_type_id = case_type.id
			LEFT JOIN pref_card_staff_item ON pref_card_staff.id = pref_card_staff_item.card_id
			LEFT JOIN pref_card_staff_note ON pref_card_staff.id = pref_card_staff_note.card_id
			WHERE case_type.organization_id=0;
		DELETE pref_card_location, pref_card_location_item, pref_card_location_note FROM pref_card_location
			LEFT JOIN case_type ON pref_card_location.case_type_id = case_type.id
			LEFT JOIN pref_card_location_item ON pref_card_location.id = pref_card_location_item.card_id
			LEFT JOIN pref_card_location_note ON pref_card_location.id = pref_card_location_note.card_id
			WHERE case_type.organization_id=0;

			ALTER TABLE `pref_card_staff` ADD `last_edit_date` DATETIME NOT NULL;
			ALTER TABLE `pref_card_staff` CHANGE `time_create` `create_date` DATETIME NOT NULL;
			ALTER TABLE `pref_card_location` ADD `last_edit_date` DATETIME NOT NULL;
			ALTER TABLE `pref_card_location` CHANGE `time_create` `create_date` DATETIME NOT NULL;
		");
	}
}
