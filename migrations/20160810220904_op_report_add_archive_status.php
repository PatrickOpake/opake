<?php

use \Console\Migration\BaseMigration;

class OpReportAddArchiveStatus extends BaseMigration
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
            ALTER TABLE `case_op_report` ADD COLUMN `is_archived` tinyint(1) NULL DEFAULT 0;
        ");

        $rows = $this->getDb()->query('select')
                ->table('case_op_report')
                ->where('status', 5)
                ->execute();

        foreach ($rows as $row) {
                $this->getDb()->query('update')->table('case_op_report')
                        ->data([
                                'is_archived' => 1,
                        ])
                        ->where('id', $row->id)
                        ->execute();
        }
    }
}
