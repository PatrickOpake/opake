<?php

use \Console\Migration\BaseMigration;

class SiteActions extends BaseMigration
{
    public function change()
    {
        $this->getDb()->query('insert')
            ->table('user_activity_action')
            ->data([
                'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_SETTINGS_ADD_SITE,
                'name' => 'Add Site',
                'zone' => 8
            ])->execute();

        $this->getDb()->query('insert')
            ->table('user_activity_action')
            ->data([
                'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_SETTINGS_EDIT_SITE,
                'name' => 'Edit Site',
                'zone' => 8
            ])->execute();

        $this->getDb()->query('insert')
            ->table('user_activity_action')
            ->data([
                'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_SETTINGS_REMOVE_SITE,
                'name' => 'Remove Site',
                'zone' => 8
            ])->execute();
    }
}
