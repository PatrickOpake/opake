<?php

use \Console\Migration\BaseMigration;

class InvQuantityLocationAction extends BaseMigration
{
	public function change()
	{
		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_INVENTORY_ADD_QUANTITY_LOCATIONS,
				'name' => 'Add Quantity/Locations',
				'zone' => 7
			])->execute();

		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_INVENTORY_REMOVE_QUANTITY_LOCATIONS,
				'name' => 'Remove Quantity/Locations',
				'zone' => 7
			])->execute();
	}
}
