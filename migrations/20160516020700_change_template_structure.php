<?php

use \Console\Migration\BaseMigration;

class ChangeTemplateStructure extends BaseMigration
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
             CREATE TABLE IF NOT EXISTS `case_op_report_custom_field` (
                    `id` int(11) NOT NULL,
                    `organization_id` int(11) NOT NULL,
                    `name` VARCHAR (255) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
                ALTER TABLE `case_op_report_custom_field` ADD PRIMARY KEY (`id`);
                ALTER TABLE `case_op_report_custom_field` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

                ALTER TABLE `case_op_report_site_template` ADD COLUMN `custom_field_id` INT(11) NULL;

                ALTER TABLE `case_op_report_fields_template` ADD COLUMN `custom_field_id` INT(11) NULL;
                ALTER TABLE `case_op_report_future_fields_template` ADD COLUMN `custom_field_id` INT(11) NULL;

                ALTER TABLE `case_op_report_custom_field_value` ADD COLUMN `custom_field_id` INT(11) NULL;
                ALTER TABLE `case_op_report_future_custom_field_value` ADD COLUMN `custom_field_id` INT(11) NULL;


        ");

        $q = $this->getDb()->query('select')->table('case_op_report_site_template')
                ->fields('id', 'organization_id', 'field', 'name')
                ->where('field', 'custom')
                ->execute();

        foreach ($q as $row) {
            $row = (array)$row;
            $this->getDb()->query('insert')->table('case_op_report_custom_field')
                    ->data([
                            'organization_id' => $row['organization_id'],
                            'name' => $row['name'],
                    ])
                    ->execute();

            $customFieldId = $this->getDb()->insert_id();

            $this->getDb()->query('update')->table('case_op_report_site_template')
                    ->data([
                            'custom_field_id' => $customFieldId,
                    ])
                    ->where('id', $row['id'])
                    ->execute();
        }

        $q = $this->getDb()->query('select')->table('case_op_report_fields_template')
                ->fields('id', 'organization_id', 'field', 'name')
                ->where('field', 'custom')
                ->execute();
        foreach ($q as $row) {
            $searchQuery = $this->getDb()->query('select')->table('case_op_report_custom_field')
                    ->fields('id', 'organization_id', 'name')
                    ->where('name', $row->name)
                    ->where('organization_id', $row->organization_id)
                    ->execute()
                    ->as_array();

            if(!empty($searchQuery)) {
                            $this->getDb()->query('update')->table('case_op_report_fields_template')
                    ->data([
                            'custom_field_id' => $searchQuery[0]->id,
                    ])
                    ->where('id', $row->id)
                    ->execute();
            } else {
                $this->getDb()->query('insert')->table('case_op_report_custom_field')
                    ->data([
                            'organization_id' => $row->organization_id,
                            'name' => $row->name,
                    ])
                    ->execute();

                $customFieldId = $this->getDb()->insert_id();

                $this->getDb()->query('update')->table('case_op_report_fields_template')
                        ->data([
                                'custom_field_id' => $customFieldId,
                        ])
                        ->where('id', $row->id)
                        ->execute();
            }

        }

        $q = $this->getDb()->query('select')->table('case_op_report_future_fields_template')
                ->fields('id', 'organization_id', 'field', 'name')
                ->where('field', 'custom')
                ->execute();
        foreach ($q as $row) {
            $searchQuery = $this->getDb()->query('select')->table('case_op_report_custom_field')
                    ->fields('id', 'organization_id', 'name')
                    ->where('name', $row->name)
                    ->where('organization_id', $row->organization_id)
                    ->execute()
                    ->as_array();

            if(!empty($searchQuery)) {
                $this->getDb()->query('update')->table('case_op_report_future_fields_template')
                        ->data([
                                'custom_field_id' => $searchQuery[0]->id,
                        ])
                        ->where('id', $row->id)
                        ->execute();
            } else {
                $this->getDb()->query('insert')->table('case_op_report_custom_field')
                        ->data([
                                'organization_id' => $row->organization_id,
                                'name' => $row->name,
                        ])
                        ->execute();

                $customFieldId = $this->getDb()->insert_id();

                $this->getDb()->query('update')->table('case_op_report_future_fields_template')
                        ->data([
                                'custom_field_id' => $customFieldId,
                        ])
                        ->where('id', $row->id)
                        ->execute();
            }

        }

        $q = $this->getDb()->query('select')->table('case_op_report_custom_field_value')
                ->fields('id', 'report_id', 'field_name')
                ->execute();
        foreach ($q as $row) {
            $reportQuery = $this->getDb()->query('select')->table('case_op_report')
                    ->fields('id', 'case_id')
                    ->where('id', $row->report_id)
                    ->execute()
                    ->as_array();
            if(!empty($reportQuery)) {
                $caseQuery = $this->getDb()->query('select')->table('case')
                        ->fields('id', 'organization_id')
                        ->where('id', $reportQuery[0]->case_id)
                        ->execute()
                        ->as_array();
                if(!empty($caseQuery)) {
                    $searchQuery = $this->getDb()->query('select')->table('case_op_report_custom_field')
                            ->fields('id', 'organization_id', 'name')
                            ->where('name', $row->field_name)
                            ->where('organization_id', $caseQuery[0]->organization_id)
                            ->execute()
                            ->as_array();
                    if(!empty($searchQuery)) {
                        $this->getDb()->query('update')->table('case_op_report_custom_field_value')
                                ->data([
                                        'custom_field_id' => $searchQuery[0]->id,
                                ])
                                ->where('id', $row->id)
                                ->execute();
                    } else {
                        $this->getDb()->query('insert')->table('case_op_report_custom_field')
                                ->data([
                                        'organization_id' => $caseQuery[0]->organization_id,
                                        'name' => $row->field_name,
                                ])
                                ->execute();

                        $customFieldId = $this->getDb()->insert_id();

                        $this->getDb()->query('update')->table('case_op_report_custom_field_value')
                                ->data([
                                        'custom_field_id' => $customFieldId,
                                ])
                                ->where('id', $row->id)
                                ->execute();
                    }
                }
            }
        }

        $q = $this->getDb()->query('select')->table('case_op_report_future_custom_field_value')
                ->fields('id', 'future_id', 'field_name')
                ->execute();
        foreach ($q as $row) {
            $reportQuery = $this->getDb()->query('select')->table('case_op_report_future')
                    ->fields('id', 'organization_id')
                    ->where('id', $row->future_id)
                    ->execute()
                    ->as_array();
            if(!empty($reportQuery)) {
                    $searchQuery = $this->getDb()->query('select')->table('case_op_report_custom_field')
                            ->fields('id', 'organization_id', 'name')
                            ->where('name', $row->field_name)
                            ->where('organization_id', $reportQuery[0]->organization_id)
                            ->execute()
                            ->as_array();
                    if(!empty($searchQuery)) {
                        $this->getDb()->query('update')->table('case_op_report_future_custom_field_value')
                                ->data([
                                        'custom_field_id' => $searchQuery[0]->id,
                                ])
                                ->where('id', $row->id)
                                ->execute();
                    } else {
                        $this->getDb()->query('insert')->table('case_op_report_custom_field')
                                ->data([
                                        'organization_id' => $reportQuery[0]->organization_id,
                                        'name' => $row->field_name,
                                ])
                                ->execute();

                        $customFieldId = $this->getDb()->insert_id();

                        $this->getDb()->query('update')->table('case_op_report_future_custom_field_value')
                                ->data([
                                        'custom_field_id' => $customFieldId,
                                ])
                                ->where('id', $row->id)
                                ->execute();
                    }
            }
        }

        $this->query("
                ALTER TABLE `case_op_report_custom_field_value` DROP `field_name`;
                ALTER TABLE `case_op_report_future_custom_field_value` DROP `field_name`;
        ");
    }
}
