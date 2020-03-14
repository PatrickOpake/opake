<?php

use \Console\Migration\BaseMigration;

class AddLocationStorage extends BaseMigration
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
		CREATE TABLE IF NOT EXISTS `location_storage` (
			`id` int(11) NOT NULL,
			`site_id` int(11) NOT NULL,
			`name` varchar(255) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		ALTER TABLE `location_storage`
 			ADD PRIMARY KEY (`id`), ADD KEY `site_id` (`site_id`);
		ALTER TABLE `location_storage`
			MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
	");

	$db = $this->getDb();
	    $db->begin_transaction();
	    try {
		    $rows = $this->getDb()->query('select')->table('location')
			    ->fields('id', 'site_id', 'name')
			    ->execute();
		    foreach ($rows as $row) {
			   $this->getDb()->query('insert')->table('location_storage')
				->data([
					'site_id' => $row->site_id,
					'name' => $row->name
				])
				->execute();
		    }
		    $db->commit();
	    } catch (\Exception $e) {
		    $db->rollback();
		    $this->writeln("can't migrate location storage");
		    throw $e;
	    }
    }
}
