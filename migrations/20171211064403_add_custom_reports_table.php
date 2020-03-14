<?php

use \Console\Migration\BaseMigration;

class AddCustomReportsTable extends BaseMigration
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
    public function up()
    {
        $this->query('
          CREATE TABLE `analytics_custom_reports` (
			  `id` INT (11) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` INT (11) NOT NULL,
			  `name` VARCHAR (30) NOT NULL,
			  `parent_id` INT (11) NOT NULL,
			  `columns` TEXT NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');
        $this->query('CREATE UNIQUE INDEX analytics_custom_reports_user_id_name_uindex 
            ON analytics_custom_reports (user_id, name)');
    }


    public function down()
    {
        $this->query('DROP TABLE `analytics_custom_reports`');
    }
}
