<?php

use \Console\Migration\BaseMigration;

class InsuranceActions extends BaseMigration
{
	public function change()
	{
		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_PATIENT_ADD_INSURANCE,
				'name' => 'Add Insurance',
				'zone' => 6
			])->execute();

		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_PATIENT_REMOVE_INSURANCE,
				'name' => 'Remove Insurance',
				'zone' => 6
			])->execute();

		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_INTAKE_ADD_INSURANCE,
				'name' => 'Add Insurance',
				'zone' => 3
			])->execute();

		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_INTAKE_REMOVE_INSURANCE,
				'name' => 'Remove Insurance',
				'zone' => 3
			])->execute();
	}
}
