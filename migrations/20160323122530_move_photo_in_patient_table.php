<?php

use \Console\Migration\BaseMigration;

class MovePhotoInPatientTable extends BaseMigration
{
    public function change()
    {
        $this->query("
            ALTER TABLE `patient` ADD COLUMN `photo_id` INT NULL DEFAULT NULL;
            ALTER TABLE `patient_user` DROP COLUMN `photo_id`;
        ");
    }
}
