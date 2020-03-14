<?php

use \Console\Migration\BaseMigration;

class UpdateBlockColors extends BaseMigration
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
		$q = $this->getDb()->query('select')->table('user')
			->fields('id', 'case_color')
			->execute();

		foreach ($q as $row) {
			$row = (array)$row;
			$user_id = $row['id'];
			$color = $row['case_color'];

			if ($color) {
				$this->getDb()->query('update')->table('case_blocking')
					->data([
						'color' => $color,
					])
					->where('doctor_id', $user_id)
					->execute();

				$this->getDb()->query('update')->table('case_blocking_item')
					->data([
						'color' => $color,
					])
					->where('doctor_id', $user_id)
					->execute();
			}
		}
	}
}
