<?php

use \Console\Migration\BaseMigration;

class ActivityAuthActions extends BaseMigration
{
    public function change()
    {
        $this->getDb()->query('insert')
            ->table('user_activity_action_zone')
            ->data([
                'id' => 9,
                'name' => 'Auth'
            ])->execute();

        $this->getDb()->query('insert')
            ->table('user_activity_action')
            ->data([
                'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_AUTH_LOGIN,
                'name' => 'Login',
                'zone' => 9
            ])->execute();

        $this->getDb()->query('insert')
            ->table('user_activity_action')
            ->data([
                'id' => \Opake\Model\Analytics\UserActivity\ActivityRecord::ACTION_AUTH_LOGOUT,
                'name' => 'Logout',
                'zone' => 9
            ])->execute();
    }
}
