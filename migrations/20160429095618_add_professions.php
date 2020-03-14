<?php

use \Console\Migration\BaseMigration;

class AddProfessions extends BaseMigration
{
    public function change()
    {
        $this->query("
            INSERT INTO `profession` (`name`) VALUES ('Physician Assistant');
            INSERT INTO `profession` (`name`) VALUES ('Nurse Anesthetist');
            INSERT INTO `profession` (`name`) VALUES ('Clinical Support');
            INSERT INTO `profession` (`name`) VALUES ('Technician');
            INSERT INTO `profession` (`name`) VALUES ('X-Ray Technician');
        ");
    }
}
