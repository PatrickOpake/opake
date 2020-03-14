<?php

namespace OpakeAdmin\Service\Scrypt\SFax;

use Opake\Helper\TimeFormat;
use OpakeAdmin\Service\Scrypt\SFax\Utils\AesEncryption;

class FaxService
{
	protected $userName;
	protected $apiKey;
	protected $encryptionKey;
	protected $encryptionInitVector;
	protected $endpointUrl;


	public function __construct()
	{
		$app = \Opake\Application::get();

		$this->endpointUrl = $app->config->get('app.scrypt_sfax_api.endpoint_url');
		$this->userName = $app->config->get('app.scrypt_sfax_api.user_name');
		$this->apiKey = $app->config->get('app.scrypt_sfax_api.api_key');
		$this->encryptionKey = $app->config->get('app.scrypt_sfax_api.encryption_key');
		$this->encryptionInitVector = $app->config->get('app.scrypt_sfax_api.encryption_init_vector');

	}

	public function checkInboundFaxes()
	{
		$app = \Opake\Application::get();
		$client = new \GuzzleHttp\Client(['base_uri' => $this->endpointUrl]);

		$res = $client->get('receiveinboundfax', [
			'headers' => [
				'Accept' => 'application/json'
			],
			'query' => [
				'token' => $this->generateSecurityToken(),
			    'ApiKey' => $this->apiKey
			],
			'verify' => true
		]);

		if ($res->getStatusCode() !== 200) {
			throw new \Exception('Unexpected status code: ' . $res->getStatusCode());
		}

		$contents = $res->getBody()->getContents();
		$json = json_decode($contents, true);

		$requestDate = new \DateTime();
		if (isset($json['InboundFaxItems'])) {
			foreach ($json['InboundFaxItems'] as $inboundFaxItem) {
				$faxId = $inboundFaxItem['FaxId'];
				$toFaxNumber = $inboundFaxItem['ToFaxNumber'];
				$fromFaxNumber = $inboundFaxItem['FromFaxNumber'];
				$faxDate = $inboundFaxItem['FaxDateIso'];

				$faxDate = \DateTime::createFromFormat('Y-m-d\TH:i:sO', $faxDate);

				$toFaxNumberWithoutCode = $toFaxNumber;
				if (strlen($toFaxNumberWithoutCode) == 11) {
					$toFaxNumberWithoutCode = substr($toFaxNumberWithoutCode, 1);
				}

				$foundSiteForFax = $app->orm->get('Site')
					->where('contact_fax', $toFaxNumberWithoutCode)
					->find();

				$organizationId = null;
				$siteId = null;
				if ($foundSiteForFax) {
					$organizationId = $foundSiteForFax->organization_id;
					$siteId = $foundSiteForFax->id();
				}

				if (strlen($toFaxNumber) == 10) {
					$fromFaxNumber = '1' . $toFaxNumber;
				}

				if (strlen($fromFaxNumber) == 10) {
					$fromFaxNumber = '1' . $fromFaxNumber;
				}

				$model = $app->orm->get('Efax_InboundFax');
				$model->organization_id = $organizationId;
				$model->site_id = $siteId;
				$model->to_fax = $toFaxNumber;
				$model->from_fax = $fromFaxNumber;
				$model->sent_date = TimeFormat::formatToDBDatetime($faxDate);
				$model->received_date = TimeFormat::formatToDBDatetime($requestDate);
				$model->scrypt_sfax_id = $faxId;
				$model->save();
			}
		}
	}

	/**
	 * @param \Opake\Model\Efax\InboundFax $inboundFax
	 * @return \Opake\Model\AbstractModel
	 * @throws \Exception
	 */
	public function downloadInboundFax($inboundFax)
	{
		$app = \Opake\Application::get();
		$client = new \GuzzleHttp\Client(['base_uri' => $this->endpointUrl]);

		$res = $client->get('downloadinboundfaxaspdf', [
			'headers' => [
				'Accept' => 'application/json'
			],
			'query' => [
				'token' => $this->generateSecurityToken(),
				'ApiKey' => $this->apiKey,
			    'FaxId' => $inboundFax->scrypt_sfax_id
			],
			'verify' => true
		]);

		if ($res->getStatusCode() !== 200) {
			throw new \Exception('Unexpected status code: ' . $res->getStatusCode());
		}

		$contents = $res->getBody()->getContents();

		$uploadedFile = $app->orm->get('UploadedFile');
		$uploadedFile->storeContent('inbound-fax-' . $inboundFax->id() . '.pdf', $contents, [
			'is_protected' => true,
		    'protected_type' => 'deny',
		    'mime_type' => 'application/pdf'
		]);

		$uploadedFile->save();
		$inboundFax->saved_file_id = $uploadedFile->id();
		$inboundFax->save();

		return $uploadedFile;
	}

	protected function generateSecurityToken()
	{
		$genDate =  gmdate("Y-m-d") . "T" . gmdate("H:i:s") . "Z";
		$fields = [
			'Context=',
		    'Username=' . $this->userName,
		    'ApiKey=' . $this->apiKey,
		    'GenDT=' . $genDate
		];
		$params = [];
		$inputData = implode('&', $fields);
		$aes = new AesEncryption($this->encryptionKey, $this->encryptionInitVector, "PKCS7", "cbc");
		return base64_encode($aes->encrypt($inputData));
	}
}