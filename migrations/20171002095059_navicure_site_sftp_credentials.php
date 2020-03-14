<?php

use \Console\Migration\BaseMigration;

class NavicureSiteSftpCredentials extends BaseMigration
{
    public function change()
    {
		$this->query("
			ALTER TABLE `site` ADD `navicure_sftp_username` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `npi`;
			ALTER TABLE `site` ADD `navicure_sftp_password` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `navicure_sftp_username`;
		");
    }
}
