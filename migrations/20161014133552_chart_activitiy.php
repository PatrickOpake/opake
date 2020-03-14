<?php

use \Console\Migration\BaseMigration;
use Opake\Model\Analytics\UserActivity\ActivityRecord;

class ChartActivitiy extends BaseMigration
{
    public function change()
    {
		/*$this->getDb()->query('delete')
			->table('user_activity_action')
			->where('id', ActivityRecord::ACTION_SETTINGS_CREATE_FORM)
			->execute();

	    $this->getDb()->query('delete')
		    ->table('user_activity_action')
		    ->where('id', ActivityRecord::ACTION_SETTINGS_EDIT_FORM)
		    ->execute();*/

	    $this->getDb()->query('insert')
		    ->table('user_activity_action_zone')
		    ->data([
			    'id' => 11,
			    'name' => 'Charts'
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => ActivityRecord::ACTION_CHART_CREATE_CHART,
			    'name' => 'Create Chart',
			    'zone' => 11
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => ActivityRecord::ACTION_CHART_UPLOAD_CHART,
			    'name' => 'Upload Chart',
			    'zone' => 11
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => ActivityRecord::ACTION_CHART_REUPLOAD_CHART,
			    'name' => 'Reupload Chart',
			    'zone' => 11
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => ActivityRecord::ACTION_CHART_EDIT_CHART,
			    'name' => 'Edit Chart',
			    'zone' => 11
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => ActivityRecord::ACTION_CHART_RENAME_CHART,
			    'name' => 'Rename Chart',
			    'zone' => 11
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => ActivityRecord::ACTION_CHART_ASSIGN_CHART,
			    'name' => 'Assign Chart',
			    'zone' => 11
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => ActivityRecord::ACTION_CHART_REMOVE_CHART,
			    'name' => 'Remove Chart',
			    'zone' => 11
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => ActivityRecord::ACTION_CHART_MOVE_CHART,
			    'name' => 'Move Chart',
			    'zone' => 11
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => ActivityRecord::ACTION_CHART_GROUP_CREATE,
			    'name' => 'Create Chart Group',
			    'zone' => 11
		    ])->execute();

	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => ActivityRecord::ACTION_CHART_GROUP_EDIT,
			    'name' => 'Edit Chart Group',
			    'zone' => 11
		    ])->execute();


	    $this->getDb()->query('insert')
		    ->table('user_activity_action')
		    ->data([
			    'id' => ActivityRecord::ACTION_CHART_GROUP_REMOVE,
			    'name' => 'Remove Chart Group',
			    'zone' => 11
		    ])->execute();

    }
}
