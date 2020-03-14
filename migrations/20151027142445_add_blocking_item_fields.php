<?php

use \Console\Migration\BaseMigration;

class AddBlockingItemFields extends BaseMigration
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
            ALTER TABLE `case_blocking_item` ADD `location_id` INT(11) NULL;
            ALTER TABLE `case_blocking_item` ADD `doctor_id` INT(11) NULL;
            ALTER TABLE `case_blocking_item` ADD `color` VARCHAR(255) DEFAULT NULL;
        ");

		$q = $this->getDb()->query('select')->table('case_blocking')
			->fields('id', 'location_id', 'doctor_id', 'color')
			->execute();
		foreach ($q as $row) {
			$row = (array)$row;

			$this->getDb()->query('update')->table('case_blocking_item')
				->data([
					'location_id' => $row['location_id'],
					'doctor_id' => $row['doctor_id'],
					'color' => $row['color'],
				])
				->where('blocking_id', $row['id'])
				->execute();
		}
	}
}
