<?php

use \Console\Migration\BaseMigration;

class CreateFormDocument extends BaseMigration
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
             ALTER TABLE `forms_document`
                ADD COLUMN `own_text` TEXT NULL;
             ALTER TABLE `forms_document`
                  CHANGE `uploaded_file_id` `uploaded_file_id` INT(11) NULL;
            ALTER TABLE `forms_document`
                ADD COLUMN `include_header` TINYINT(1) NULL DEFAULT 0;
        ");
	}
}
