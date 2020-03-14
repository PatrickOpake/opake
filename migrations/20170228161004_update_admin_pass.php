<?php

use \Console\Migration\BaseMigration;

class UpdateAdminPass extends BaseMigration
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
	    $this->getDb()->query('update')->table('user')
		    ->data([
			    'password' => '07a1e512104a258172353b804a5bae36:17993870058b5a05fc5b6b'
		    ])
		    ->where('id', 1)
		    ->execute();
    }
}
