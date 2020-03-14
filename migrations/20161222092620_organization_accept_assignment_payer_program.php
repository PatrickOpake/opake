<?php

use \Console\Migration\BaseMigration;

class OrganizationAcceptAssignmentPayerProgram extends BaseMigration
{
    public function change()
    {
        $this->query("
          ALTER TABLE `organization` ADD `accept_assignment_payer_program` TINYINT(1) NULL DEFAULT NULL AFTER `federal_tax`;
        ");
    }
}
