<?php

use \Console\Migration\BaseMigration;

class FormOwnTextToMediumtext extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `forms_document`
                CHANGE COLUMN `own_text` `own_text` MEDIUMTEXT NULL AFTER `name`;
        ");
    }
}
