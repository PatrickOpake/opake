<?php

use \Console\Migration\BaseMigration;

class CredentialsAlert extends BaseMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
    	$this->query("
    		CREATE TABLE IF NOT EXISTS `user_credentials_alert` (
                `id` INT(11) AUTO_INCREMENT,
                `credentials_id` INT(11) NOT NULL,
                `field` VARCHAR(255) NULL NOT NULL,
                `status` TINYINT(1) NOT NULL DEFAULT 1,                
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;
    	");
    }
}
