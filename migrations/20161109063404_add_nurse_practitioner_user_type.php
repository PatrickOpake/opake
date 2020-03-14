<?php

use \Console\Migration\BaseMigration;

class AddNursePractitionerUserType extends BaseMigration
{
    public function change()
    {
        $this->query("
            INSERT INTO `profession` (`name`) VALUES ('Nurse Practitioner');
        ");
    }
}
