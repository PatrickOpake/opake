<?php

use \Console\Migration\BaseMigration;

class OpReportTemplateActions extends BaseMigration
{
	public function change()
	{
		$this->getDb()->query('insert')
			->table('user_activity_action')
			->data([
				'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_SETTINGS_ADD_OPERATIVE_REPORT_TEMPLATE,
				'name' => 'Add Operative Report Template',
				'zone' => 8
			])->execute();

	}
}
