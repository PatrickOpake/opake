<?php

use \Console\Migration\BaseMigration;

class InventoryMultiplier extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE IF NOT EXISTS `inventory_multiplier` (
                `id` INT(11) AUTO_INCREMENT,
                `organization_id` INT(11) NOT NULL,
                `type` INT(11) NULL DEFAULT 0,
                `inventory_id` INT(11) NULL DEFAULT NULL,
                `inventory_type_id` INT(11) NULL DEFAULT NULL,
                `multiplier` FLOAT NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;
            
        ");
    }
}
