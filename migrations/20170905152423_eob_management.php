<?php

use \Console\Migration\BaseMigration;

class EobManagement extends BaseMigration
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
		CREATE TABLE `billing_eob` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `organization_id` int(11) DEFAULT NULL,
			  `insurer_id` int(11) NULL DEFAULT NULL,
			  `name` VARCHAR (255) NULL DEFAULT NULL,
			  `charge_master_id` int(11) NULL DEFAULT NULL,
			  `charge_master_amount` float NULL DEFAULT NULL,
			  `amount_reimbursed` float DEFAULT NULL,
			  `uploaded_file_id` int(11) NULL DEFAULT NULL,
		          `remote_file_id` INT(10) NULL DEFAULT NULL,
			  `uploaded_date` DATETIME NULL DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	");
    }
}
