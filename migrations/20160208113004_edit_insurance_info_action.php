<?php

use \Console\Migration\BaseMigration;

class EditInsuranceInfoAction extends BaseMigration
{
	public function change()
	{
		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_PATIENT_EDIT_INSURANCE,
				'name' => 'Edit Insurance Info',
				'zone' => 6
			])->execute();
	}
}
