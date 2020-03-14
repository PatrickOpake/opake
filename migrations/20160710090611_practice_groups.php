<?php

use \Console\Migration\BaseMigration;

class PracticeGroups extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE `practice_groups` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL,
                `active` TINYINT(1) NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");

        $this->query("
            CREATE TABLE `organization_practice_groups` (
                `organization_id` INT(10) NULL,
                `practice_group_id` INT(10) NULL,
                PRIMARY KEY (`organization_id`, `practice_group_id`)
            )
            ENGINE=InnoDB;
        ");

        $this->query("
            CREATE TABLE `user_practice_groups` (
                `user_id` INT(10) NULL,
                `practice_group_id` INT(10) NULL,
                PRIMARY KEY (`user_id`, `practice_group_id`)
            )
            ENGINE=InnoDB;
        ");
    }
}
