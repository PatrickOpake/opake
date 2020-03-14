<?php

use \Console\Migration\BaseMigration;
use Opake\Model\Cases\OperativeReport\SiteTemplate;

class AddNewTemplates extends BaseMigration
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
        $rows = $this->getDb()->query('select')
                ->table('case_op_report_future_fields_template')
                ->group_by('future_template_id')
                ->execute();

        foreach ($rows as $row) {
            foreach (SiteTemplate::fieldsProps() as $key => $field) {
                if($field['show'] == 'only_future') {
                    $this->getDb()->query('insert')->table('case_op_report_future_fields_template')
                            ->data([
                                    'organization_id' => $row->organization_id,
                                    'future_template_id' => $row->future_template_id,
                                    'group_id' => $field['group_id'],
                                    'field' => $key,
                                    'type' => SiteTemplate::getTypeByField($key),
                                    'name' => SiteTemplate::getNameByField($key),
                                    'sort' => SiteTemplate::getSortByField($key),
                                    'show' => SiteTemplate::getShowByField($key),
                                    'active' => true,
                            ])
                            ->execute();
                }

            }

        }
    }
}
