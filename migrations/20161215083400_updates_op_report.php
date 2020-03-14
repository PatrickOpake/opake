<?php

use \Console\Migration\BaseMigration;

class UpdatesOpReport extends BaseMigration
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
		ALTER TABLE `case_op_report_future_fields_template` ADD COLUMN `group_id` INT(11) NULL DEFAULT NULL AFTER `name`;
		ALTER TABLE `case_op_report_fields_template` DROP `on_page`;

    	");

	$db = $this->getDb();
	$db->begin_transaction();
	try {
	    $rows = $this->getDb()->query('select')->table('case_op_report_future_fields_template')
		    ->fields('id', 'group_id', 'field')
		    ->execute();
	    foreach ($rows as $row) {
	    	if($row->field === 'custom' || $row->field === 'list') {
			$group_id = \Opake\Model\Cases\OperativeReport\SiteTemplate::GROUP_FOLLOW_UP_ID;

		} else {
			$group_id = \Opake\Model\Cases\OperativeReport\SiteTemplate::getGroupIdByField($row->field);
		}
		    $this->getDb()->query('update')->table('case_op_report_future_fields_template')
			    ->data([
				    'group_id' => $group_id,
			    ])
			    ->where('id', $row->id)
			    ->execute();
	    }
	    $db->commit();
	} catch (\Exception $e) {
	    $db->rollback();
	    $this->writeln("can't update case_op_report_future_fields_template");
	    throw $e;
	}

	    $db->begin_transaction();
	    try {
		    $rows = $this->getDb()->query('select')->table('case_op_report_fields_template')
			    ->fields('id', 'group_id', 'field')
			    ->execute();
		    foreach ($rows as $row) {
		    	if(!$row->group_id) {
				if($row->field === 'custom' || $row->field === 'list') {
					$group_id = \Opake\Model\Cases\OperativeReport\SiteTemplate::GROUP_FOLLOW_UP_ID;

				} else {
					$group_id = \Opake\Model\Cases\OperativeReport\SiteTemplate::getGroupIdByField($row->field);
				}
				$this->getDb()->query('update')->table('case_op_report_fields_template')
					->data([
						'group_id' => $group_id,
					])
					->where('id', $row->id)
					->execute();
			}
		    }
		    $db->commit();
	    } catch (\Exception $e) {
		    $db->rollback();
		    $this->writeln("can't update case_op_report_future_fields_template");
		    throw $e;
	    }

    }
}
