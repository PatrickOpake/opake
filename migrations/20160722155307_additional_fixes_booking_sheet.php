<?php

use \Console\Migration\BaseMigration;

class AdditionalFixesBookingSheet extends BaseMigration
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
            CREATE TABLE IF NOT EXISTS `booking_pre_op_required_data` (
                `pre_op_required` int(11) NOT NULL,
                `booking_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `booking_pre_op_required_data` ADD UNIQUE KEY `uni` (`pre_op_required`,`booking_id`);
            
             CREATE TABLE IF NOT EXISTS `booking_studies_ordered` (
                `studies_order` int(11) NOT NULL,
                `booking_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ALTER TABLE `booking_studies_ordered` ADD UNIQUE KEY `uni` (`studies_order`,`booking_id`);
            
            ALTER TABLE `booking_sheet` DROP `pre_op_data_required`, DROP `studies_ordered`;
            
            CREATE TABLE `booking_assistant` (
              `assistant_id` int(11) NOT NULL,
              `booking_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            ALTER TABLE `booking_assistant`
              ADD UNIQUE KEY `uni` (`assistant_id`,`booking_id`);
              
              ALTER TABLE `booking_sheet` ADD COLUMN `admission_type` TINYINT(4) NULL DEFAULT NULL;
              
              CREATE TABLE `booking_secondary_diagnosis` (
              `id` int(11) NOT NULL,
              `booking_id` int(11) NOT NULL,
              `diagnosis_id` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

            ALTER TABLE `booking_secondary_diagnosis`
              ADD PRIMARY KEY (`id`),
              ADD KEY `booking_id` (`booking_id`),
              ADD KEY `diagnosis_id` (`diagnosis_id`);
            
            
            ALTER TABLE `booking_secondary_diagnosis`
              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

        ");
    }
}
