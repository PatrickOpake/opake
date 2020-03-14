<?php

use \Console\Migration\BaseMigration;

class HcpcByYear extends BaseMigration
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
	CREATE TABLE IF NOT EXISTS `hcpc_year` (
                `id` int(11) NOT NULL,
                `year` int(11) NULL DEFAULT NULL,
                `note` varchar(255) NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `hcpc_year` ADD PRIMARY KEY (`id`);
            ALTER TABLE `hcpc_year` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
            
      	CREATE TABLE IF NOT EXISTS `hcpc_to_hcpc_year` (
                `hcpc_id` int(11) NOT NULL,
                `year_id` int(11) NOT NULL,
                `active` tinyint(2) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `hcpc_to_hcpc_year` ADD PRIMARY KEY (`hcpc_id`, `year_id`);
            
            INSERT INTO hcpc_year (year) VALUES (2017);
	");

	    $cptRows = $this->getDb()->query('select')
		    ->table('hcpc')
		    ->execute();

	    $this->getDb()->begin_transaction();

	    try {
		    foreach ($cptRows as $row) {
			    $this->getDb()->query('insert')
				    ->table('hcpc_to_hcpc_year')
				    ->data([
					    'hcpc_id' => $row->id,
					    'year_id' => 1,
					    'active' => 1
				    ])->execute();
		    }

		    $this->getDb()->commit();
	    } catch (\Exception $e) {
		    $this->getDb()->rollback();
		    throw $e;
	    }
    }
}
