<?php

use \Console\Migration\BaseMigration;

class AddNewUserTypes extends BaseMigration
{
    public function change()
    {
        $this->query("
            INSERT INTO `profession` (`name`) VALUES ('Surgical Technologist');
            INSERT INTO `profession` (`name`) VALUES ('Sterile Processing Technician');
            INSERT INTO `profession` (`name`) VALUES ('Administrative Support');
            INSERT INTO `profession` (`name`) VALUES ('Secretary');
        ");
    }
}