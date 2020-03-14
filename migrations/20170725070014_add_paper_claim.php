<?php

use \Console\Migration\BaseMigration;

class AddPaperClaim extends BaseMigration
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
			CREATE TABLE `billing_paper_claim` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `case_id` int(11) DEFAULT NULL,
			  `billing_date` DATETIME  NULL  DEFAULT NULL,
			  `insurance_payer_id` int(11) NULL DEFAULT NULL,
			  `type` tinyint(2) NULL DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB;
	    ");
    }
}
