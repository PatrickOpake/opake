<?php

use \Console\Migration\BaseMigration;

class CreateFormUpload extends BaseMigration
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
                CREATE TABLE IF NOT EXISTS `forms_document` (
		  `id` int(11) NOT NULL,
		  `organization_id` int(11) NOT NULL,
		  `uploaded_file_id` int(11) NOT NULL,
		  `segment` VARCHAR(48) NOT NULL,
                  `type` VARCHAR(48) NOT NULL,
                  `name` VARCHAR(255) NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;

		ALTER TABLE `forms_document` ADD PRIMARY KEY (`id`), ADD KEY `organization_id` (`organization_id`);
		ALTER TABLE `forms_document` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

		CREATE TABLE IF NOT EXISTS `forms_document_site` (
			`doc_id` int(11) NOT NULL,
			`site_id` int(11) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		ALTER TABLE `forms_document_site`
 			ADD UNIQUE KEY `uni` (`doc_id`,`site_id`);


        ");
	}
}
