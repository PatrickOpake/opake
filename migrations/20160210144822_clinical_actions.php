<?php

use \Console\Migration\BaseMigration;

class ClinicalActions extends BaseMigration
{
	public function change()
	{
		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_ADD_CHECKLIST_ITEM,
				'name' => 'Add Checklist Note',
				'zone' => 4
			])->execute();

		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_EDIT_CHECKLIST_ITEM,
				'name' => 'Edit Checklist Note',
				'zone' => 4
			])->execute();

		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_REMOVE_CHECKLIST_ITEM,
				'name' => 'Remove Checklist Note',
				'zone' => 4
			])->execute();

		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_ADD_INVENTORY_ITEM,
				'name' => 'Add Inventory Item',
				'zone' => 4
			])->execute();

		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_EDIT_INVENTORY_ITEM,
				'name' => 'Edit Inventory Item',
				'zone' => 4
			])->execute();

		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_CLINICAL_REMOVE_INVENTORY_ITEM,
				'name' => 'Remove Inventory Item',
				'zone' => 4
			])->execute();
	}
}
