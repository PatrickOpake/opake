<?php

use \Console\Migration\BaseMigration;

class AddAddmitingDiagnosis extends BaseMigration
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
            CREATE TABLE IF NOT EXISTS `case_registration_admitting_diagnosis` (
                `id` int(11) NOT NULL,
                `reg_id` int(11) NOT NULL,
                `diagnosis_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `case_registration_admitting_diagnosis`
            ADD PRIMARY KEY (`id`),
            ADD KEY `reg_id` (`reg_id`),
            ADD KEY `diagnosis_id` (`diagnosis_id`);
            ALTER TABLE `case_registration_admitting_diagnosis` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");

        $rows = $this->getDb()->query('select')
                ->table('case_registration')
                ->execute();

        foreach ($rows as $row) {
            if($row->admitting_diagnosis_id) {
                $this->getDb()->query('insert')->table('case_registration_admitting_diagnosis')
                        ->data([
                                'reg_id' => $row->id,
                                'diagnosis_id' => $row->admitting_diagnosis_id,
                        ])
                        ->execute();
            }
        }

        $this->query("
          ALTER TABLE `case_registration` DROP `admitting_diagnosis_id`;
        ");
    }
}
