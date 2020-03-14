<?php

namespace OpakeAdmin\Service\Navicure\Claims;

use Opake\Helper\TimeFormat;
use Opake\Model\Billing\Navicure\Claim;
use OpakeAdmin\Service\Navicure\Claims\IncomingFiles\AbstractIncomingFile;
use OpakeAdmin\Service\Navicure\Claims\SFTP\Agent;

class ResponseHandler
{
	public function handleIncomingFiles()
	{
		$app = \Opake\Application::get();
		$activeClaimCount = $app->db->query('count')
			->fields('id')
			->table('billing_navicure_claim')
			->where('active', 1)
			->where('status', '!=', Claim::STATUS_PAYMENT_DENIED)
			->where('status', '!=', Claim::STATUS_PAYMENT_PROCESSED)
			->where([
				['type', \Opake\Model\Billing\Navicure\Claim::TYPE_ELECTRONIC_UB04_CLAIM],
				['or', ['type', \Opake\Model\Billing\Navicure\Claim::TYPE_ELECTRONIC_1500_CLAIM]],
			])
			->execute();

		if ($activeClaimCount == 0) {
			return;
		}

		$credentialRows = $app->db->query('select')
			->table('site')
			->fields('navicure_sftp_username', 'navicure_sftp_password')
			->where('active', 1)
			->execute();

		$credentials = [];
		foreach ($credentialRows as $row) {
			if ($row->navicure_sftp_username && $row->navicure_sftp_password) {
				$credentials[] = $row->navicure_sftp_username . '|' . $row->navicure_sftp_password;
			}
 		}

		$credentials = array_unique($credentials);
		$incomingFiles = [];

		if ($credentials) {
			foreach ($credentials as $loginPasswordSet) {
				try {
					$loginPasswordSet = explode('|', $loginPasswordSet, 2);
					$login = $loginPasswordSet[0];
					$password = $loginPasswordSet[1];

					$agent = new Agent();
					$agent->setUsernameAndPassword($login, $password);
					$agent->connect();
					$incomingFiles = array_merge($incomingFiles, $agent->fetchIncomingFiles());
				} catch (\Exception $e) {
					$app->logger->exception($e);
				}
			}
		}

		$dateTime = new \DateTime();

		/* hack for a phpseclib issue */
		$app->db->refresh_connection();

		if ($incomingFiles) {
			/** @var AbstractIncomingFile $file */
			foreach ($incomingFiles as $file) {
				$this->handleSingleIncomingFile($file);
			}
		}

		$app->db->query('update')
			->table('billing_navicure_claim')
			->data([
				'last_update' => TimeFormat::formatToDBDatetime($dateTime)
			])
			->where('active', 1)
			->where('status', '!=', Claim::STATUS_PAYMENT_DENIED)
			->where('status', '!=', Claim::STATUS_PAYMENT_PROCESSED)
			->where([
				['type', \Opake\Model\Billing\Navicure\Claim::TYPE_ELECTRONIC_UB04_CLAIM],
				['or', ['type', \Opake\Model\Billing\Navicure\Claim::TYPE_ELECTRONIC_1500_CLAIM]],
			])
			->execute();

	}

	/**
	 * @param AbstractIncomingFile $file
	 */
	public function handleSingleIncomingFile($file)
	{
		$app = \Opake\Application::get();
		$dateTime = new \DateTime();
		/** @var \Opake\Model\Billing\Navicure\Log $logRecord */
		$logRecord = $app->orm->get('Billing_Navicure_Log');
		$logRecord->claim_id = null;
		$logRecord->transaction = $file->getTransactionId();
		$logRecord->direction = \Opake\Model\Billing\Navicure\Log::DIRECTION_IN;
		$logRecord->time = TimeFormat::formatToDBDatetime($dateTime);
		$logRecord->data = $file->getContent();
		$logRecord->save();

		try {

			$rootSegment = $file->parse();
			$file->handle($rootSegment, $logRecord);

		} catch (\Exception $e) {

			$app->logger->exception($e);
			$logRecord->error = $e->getMessage();
			$logRecord->save();

		}
	}

}