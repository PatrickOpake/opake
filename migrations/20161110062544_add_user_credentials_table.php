<?php

use \Console\Migration\BaseMigration;

class AddUserCredentialsTable extends BaseMigration
{
    public function change()
    {
        $this->query("
            CREATE TABLE IF NOT EXISTS `user_credentials` (
                `id` INT(11) AUTO_INCREMENT,
                `user_id` INT(11) NOT NULL,
                `npi_number` VARCHAR(255) DEFAULT NULL,
                `npi_file_id` INT(11) NULL DEFAULT NULL,
                `medical_licence_number` VARCHAR(255) DEFAULT NULL,
                `medical_licence_exp_date` DATE NULL DEFAULT NULL,
                `medical_licence_file_id` INT(11) NULL DEFAULT NULL,
                `dea_number` VARCHAR(255) DEFAULT NULL,
                `dea_exp_date` DATE NULL DEFAULT NULL,
                `dea_file_id` INT(11) NULL DEFAULT NULL,
                `cds_number` VARCHAR(255) DEFAULT NULL,
                `cds_exp_date` DATE NULL DEFAULT NULL,
                `cds_file_id` INT(11) NULL DEFAULT NULL,
                `ecfmg` VARCHAR(255) DEFAULT NULL,
                `insurance` VARCHAR(255) DEFAULT NULL,
                `insurance_exp_date` DATE NULL DEFAULT NULL,
                `insurance_reappointment_date` DATE NULL DEFAULT NULL,
                `insurance_file_id` INT(11) NULL DEFAULT NULL,
                `acls_date` DATE NULL DEFAULT NULL,
                `acls_file_id` INT(11) NULL DEFAULT NULL,
                `immunizations_ppp_due` DATE NULL DEFAULT NULL,
                `immunizations_help_b` DATE NULL DEFAULT NULL,
                `immunizations_rubella` DATE NULL DEFAULT NULL,
                `immunizations_rubeola` DATE NULL DEFAULT NULL,
                `immunizations_varicela` DATE NULL DEFAULT NULL,
                `immunizations_mumps` DATE NULL DEFAULT NULL,
                `immunizations_flue` DATE NULL DEFAULT NULL,
                `immunizations_file_id` INT(11) NULL DEFAULT NULL,
                `retest_date` DATE NULL DEFAULT NULL,
                `upin` VARCHAR(255) DEFAULT NULL, 
                `licence_number` VARCHAR(255) DEFAULT NULL,
                `licence_expr_date` DATE NULL DEFAULT NULL,
                `licence_file_id` INT(11) NULL DEFAULT NULL,
                `bls_date` DATE NULL DEFAULT NULL,
                `bls_file_id` INT(11) NULL DEFAULT NULL,
                `cnor_date` DATE NULL DEFAULT NULL,
                `cnor_file_id` INT(11) NULL DEFAULT NULL,
                `malpractice` VARCHAR(255) DEFAULT NULL,
                `malpractice_exp_date` DATE NULL DEFAULT NULL,
                `malpractice_file_id` INT(11) NULL DEFAULT NULL,
                `hp_exp_date` DATE NULL DEFAULT NULL,
                `hp_file_id` INT(11) NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB;
        ");

        $db = $this->getDb();
        $db->begin_transaction();

        $rows = $db->query('select')
            ->table('user')
            ->fields('id', 'npi_number', 'dea_number', 'dea_number_exp_date', 'medical_licence_number',
                'medical_licence_number_exp_date', 'cds_number', 'cds_number_exp_date')
            ->execute();

        try {
            foreach ($rows as $row) {
                $db->query('insert')
                    ->table('user_credentials')
                    ->data([
                        'user_id' => $row->id,
                        'npi_number' => $row->npi_number,
                        'medical_licence_number' => $row->medical_licence_number,
                        'medical_licence_exp_date' => $row->medical_licence_number_exp_date,
                        'dea_number' => $row->dea_number,
                        'dea_exp_date' => $row->dea_number_exp_date,
                        'cds_number' => $row->cds_number,
                        'cds_exp_date' => $row->cds_number_exp_date
                    ])
                    ->execute();
            }

            $db->commit();

        } catch (\Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
}
