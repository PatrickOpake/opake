<?php

namespace OpakeAdmin\Service\Navicure\Claims\SFTP;

use OpakeAdmin\Service\Navicure\Claims\IncomingFiles\E277ClaimStatus;
use OpakeAdmin\Service\Navicure\Claims\IncomingFiles\E835ClaimPayment;
use OpakeAdmin\Service\Navicure\Claims\IncomingFiles\E997Acknowledgment;

class Agent
{
	/**
	 * @var \phpseclib\Net\SFTP
	 */
	protected $sftp;

	/**
	 * @var bool
	 */
	protected $deleteFiles = true;

	/**
	 * @var string
	 */
	protected $username;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * Agent constructor.
	 */
	public function __construct()
	{
		$app = \Opake\Application::get();
		if ($app->config->has('app.navicure_api.sftp.disable_removing')) {
			$this->deleteFiles = !$app->config->has('app.navicure_api.sftp.disable_removing');
		}
	}

	public function setUsernameAndPassword($usermame, $password)
	{
		$this->username = $usermame;
		$this->password = $password;
	}

	public function connect()
	{
		if (!$this->username || !$this->password) {
			throw new \Exception('Can\'t connect to Navicure SFTP server: Credentials are empty');
		}

		$app = \Opake\Application::get();
		$host = $app->config->get('app.navicure_api.sftp.host');

		$this->sftp = new \phpseclib\Net\SFTP($host);
		if (!$this->sftp->login($this->username, $this->password)) {
			throw new \Exception('Can\'t login to Navicure SFTP server');
		}
	}


	public function putNewClaim($data)
	{
		$this->checkIsConnected();

		if (!$this->sftp->chdir('/IN')) {
			throw new \Exception('Cannot change directory');
		}

		$filename = uniqid('CLM-') . '.837';
		if (!$this->sftp->put($filename, $data)) {
			throw new \Exception('Cannot write file');
		}
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	public function fetchIncomingFiles()
	{

		$this->checkIsConnected();

		$files = [];
		$directoriesToCheck = [
			'997' => '/OUT/997',
			'277' => '/OUT/277',
		    '835' => '/OUT/835'
		];

		foreach ($directoriesToCheck as $fileType => $path) {
			if (!$this->sftp->chdir($path)) {
				throw new \Exception('Cannot change directory: ' . $path);
			}

			foreach ($this->sftp->nlist() as $fileName) {
				if ($fileName !== '.' && $fileName !== '..') {
					$content = $this->sftp->get($fileName);
					if ($content === false) {
						throw new \Exception('Cannot download file: ' . $fileName);
					}
					if ($this->deleteFiles) {
						if (!$this->sftp->delete($fileName)) {
							throw new \Exception('Cannot delete file: ' . $fileName);
						}
					}

					$file = null;

					if ($fileType == '277') {
						$file = new E277ClaimStatus($content);
					}
					if ($fileType == '835') {
						$file = new E835ClaimPayment($content);
					}
					if ($fileType == '997') {
						$file = new E997Acknowledgment($content);
					}

					if ($file) {
						$files[] = $file;
					}
				}
			}
		}

		return $files;
	}

	protected function checkIsConnected()
	{
		if ($this->sftp === null) {
			$this->connect();
		}
	}
}