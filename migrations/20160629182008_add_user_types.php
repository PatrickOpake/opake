<?php

use \Console\Migration\BaseMigration;

class AddUserTypes extends BaseMigration
{
    public function change()
    {
        $this->getDb()->query('insert')
            ->table('profession')
            ->data([
                'name' => 'X-Ray Technologist'
            ])->execute();

        $this->getDb()->query('insert')
            ->table('profession')
            ->data([
                'name' => 'Interventionist'
            ])->execute();

        $this->getDb()->query('insert')
            ->table('profession')
            ->data([
                'name' => 'Environmental Impairment Coordinator'
            ])->execute();
    }
}
