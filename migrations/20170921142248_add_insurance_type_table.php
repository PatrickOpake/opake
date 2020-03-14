<?php

use \Console\Migration\BaseMigration;

class AddInsuranceTypeTable extends BaseMigration
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
	CREATE TABLE `insurance_type_name` (
  		`id` int(11) NOT NULL,
  		`name` varchar(45) DEFAULT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	
	INSERT INTO `insurance_type_name` (`id`, `name`) VALUES (1, 'Commercial');
	INSERT INTO `insurance_type_name` (`id`, `name`) VALUES (2, 'Medicare');
	INSERT INTO `insurance_type_name` (`id`, `name`) VALUES (3, 'Medicaid');
	INSERT INTO `insurance_type_name` (`id`, `name`) VALUES (4, 'No-Fault');
	INSERT INTO `insurance_type_name` (`id`, `name`) VALUES (5, 'Self-Pay');
	INSERT INTO `insurance_type_name` (`id`, `name`) VALUES (6, 'Workers Comp');
	INSERT INTO `insurance_type_name` (`id`, `name`) VALUES (7, 'Other');
	INSERT INTO `insurance_type_name` (`id`, `name`) VALUES (8, 'Auto Accident / No-Fault');
	INSERT INTO `insurance_type_name` (`id`, `name`) VALUES (9, 'LOP');
	INSERT INTO `insurance_type_name` (`id`, `name`) VALUES (10, 'Tricare');
	INSERT INTO `insurance_type_name` (`id`, `name`) VALUES (11, 'CHAMPVA');
	INSERT INTO `insurance_type_name` (`id`, `name`) VALUES (12, 'FECA Black Lung');
	INSERT INTO `insurance_type_name` (`id`, `name`) VALUES (13, 'Self-Funded');
	");
    }
}
