<?php

use \Console\Migration\BaseMigration;

class MoveFieldsToCredentials extends BaseMigration
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
			ALTER TABLE `user_credentials` ADD COLUMN `taxonomy_code` VARCHAR(50) NULL DEFAULT NULL;
			ALTER TABLE `user_credentials` ADD COLUMN `tin` VARCHAR(50) NULL DEFAULT NULL;
		");

	    $db = $this->getDb();
	    $db->begin_transaction();

	    $rows = $db->query('select')
		    ->table('user')
		    ->fields('id', 'npi', 'tin', 'taxonomy_code')
		    ->execute();

	    try {
		    foreach ($rows as $row) {
			    $db->query('update')
				    ->table('user_credentials')
				    ->data([
					    'npi_number' => $row->npi,
					    'tin' => $row->tin,
					    'taxonomy_code' => $row->taxonomy_code,
				    ])
				    ->where('user_id', $row->id)
				    ->execute();
		    }

		    $db->commit();

	    } catch (\Exception $e) {
		    $db->rollback();
		    throw $e;
	    }

	    $this->query("
			ALTER TABLE `user` DROP `npi`;
			ALTER TABLE `user` DROP `tin`;
			ALTER TABLE `user` DROP `insurance_provider_number`;
			ALTER TABLE `user` DROP `taxonomy_code`;
		");
    }
}
