<?php

use \Console\Migration\BaseMigration;

class OrgPermission extends BaseMigration
{
	public function change()
	{
		$this->query("
            CREATE TABLE `organization_permission` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `organization_id` INT(10) NULL DEFAULT NULL,
                `permission` VARCHAR(255) NULL DEFAULT NULL,
                `allowed` TINYINT NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");
	}
}
