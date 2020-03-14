<?php

use \Console\Migration\BaseMigration;

class CaseRegDocumentTypes extends BaseMigration
{
	public function change()
	{
		$this->query("
             CREATE TABLE `case_registration_document_types` (
                `id` INT(10) NULL AUTO_INCREMENT,
                `name` VARCHAR (255) NULL DEFAULT NULL,
                `is_required` TINYINT NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            )
            ENGINE=InnoDB;
        ");

		$initialData = [
			[1, 'Assignment of Benefits', 1],
			[2, 'Advanced Beneficiary Notice', 1],
			[3, 'Consent for Anesthesia', 1],
			[4, 'Smoking Status', 1],
			[5, 'HIPAA Acknowledgement', 1],
		];

		foreach ($initialData as $row) {
			$this->getDb()->query('insert')->table('case_registration_document_types')
				->data([
					'id' => $row[0],
					'name' => $row[1],
					'is_required' => $row[2]
				])
				->execute();
		}


	}
}
