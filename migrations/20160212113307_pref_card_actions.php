<?php

use \Console\Migration\BaseMigration;

class PrefCardActions extends BaseMigration
{
	public function change()
	{
		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_SETTINGS_EDIT_PREFERENCE_CARDS,
				'name' => 'Edit Preference Cards',
				'zone' => 8
			])->execute();

		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_ADD_PREFERENCE_CARDS,
				'name' => 'Add Preference Card',
				'zone' => 1
			])->execute();

		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_SETTINGS_ADD_PREFERENCE_CARDS,
				'name' => 'Add Preference Card',
				'zone' => 8
			])->execute();

		$this->getDb()->query('update')
			->table('user_activity_action')
			->data([
				'name' => 'Edit Preference Cards'
			])
			->where('id', 5)
			->execute();
	}
}
