<?php

use \Console\Migration\BaseMigration;

class AddSatelliteOfficeUserRole extends BaseMigration
{
    public function change()
    {
        $this->query("
            INSERT INTO `role` (`id`, `name`) VALUES (7, 'Satellite Office');
        ");
    }
}
