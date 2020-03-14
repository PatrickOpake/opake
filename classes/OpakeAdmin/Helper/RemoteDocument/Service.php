<?php

namespace OpakeAdmin\Helper\RemoteDocument;

class Service
{
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var string
	 */
	protected $apiKey;

	/**
	 * @var string
	 */
	protected $apiMasterKey;

	/**
	 * @var string
	 */
	protected $apiBaseUrl;

	/**
	 * @var \Rokolabs\ROKOMobi\ContentService
	 */
	protected $contentService;

	/**
	 * @param \Opake\Application $pixie
	 */
	public function __construct($pixie)
	{
		$this->pixie = $pixie;

		$config = $this->pixie->config;
		$this->apiKey = $config->get('app.rokomobi_api.api_key');
		$this->apiMasterKey = $config->get('app.rokomobi_api.api_master_key');
		$this->apiBaseUrl = $config->get('app.rokomobi_api.base_url');

		$credential = new \Rokolabs\ROKOMobi\Credential($this->apiKey, $this->apiMasterKey, $this->apiBaseUrl);
		$client = new \Rokolabs\ROKOMobi\Client($credential);
		$userSession = new \Rokolabs\ROKOMobi\Service\UserSession($client);
		$contentService = new \Rokolabs\ROKOMobi\ContentService($client, $userSession);

		$this->contentService = $contentService;
	}

	/**
	 * @param string $name
	 * @param string $path
	 * @param string $contentType
	 * @param bool $isPublicRead
	 * @return \Rokolabs\ROKOMobi\Result\GetContentItem
	 * @throws \Exception
	 */
	public function uploadFile($name, $path, $contentType, $isPublicRead = false)
	{
		$file = new \Rokolabs\ROKOMobi\Dto\FileDto();
		$file->name = $name;
		$file->contentType = $contentType;
		$file->isPublicRead = $isPublicRead;

		$createdServiceFile = $this->contentService->createFile($file);

		clearstatcache();
		$size = filesize($path);
		$fp = fopen($path, 'r');

		$uploadFile = new \Rokolabs\ROKOMobi\ClientParams\UploadFile($fp, $size);
		$uploadInfo = $createdServiceFile->getUploadInfo();

		$uploadInfoDto = new \Rokolabs\ROKOMobi\Dto\UploadInfoDto();
		$uploadInfoDto->url = $uploadInfo->getUrl();
		$uploadInfoDto->headers = $uploadInfo->getFullHeaders();
		$uploadInfoDto->httpMethod = $uploadInfo->getHttpMethod();

		$this->contentService->uploadFile($uploadFile, $uploadInfoDto);

		if (is_resource($fp)) {
			fclose($fp);
		}

		$contentItemDto = new \Rokolabs\ROKOMobi\Dto\ContentItemDto();
		$contentItemDto->name = $name;
		$contentItemDto->status = 'active';

		$assetDto = new \Rokolabs\ROKOMobi\Dto\AssetDto();
		$assetDto->assetType = new \Rokolabs\ROKOMobi\Dto\AssetTypeDto();
		$assetDto->assetType->objectId = $this->getPrimaryAssetType()->getObjectId();
		$assetDto->name = $name;
		$assetDto->file = new \Rokolabs\ROKOMobi\Dto\FileDto();
		$assetDto->file->objectId = $createdServiceFile->getObjectId();

		$contentItemDto->assets = [
			$assetDto
		];

		$contentItemResult = $this->contentService->createContentItem($contentItemDto);

		$serviceContentItem =  $this->contentService->getContentItem($contentItemResult->getObjectId());

		return $serviceContentItem;
	}

	/**
	 * @param $id
	 * @return bool
	 */
	public function deleteContentItem($id)
	{
		return $this->contentService->deleteContentItem($id);
	}

	/**
	 * @param array $assetIds
	 * @return \Rokolabs\ROKOMobi\Result\DocumentOperationBegin
	 */
	public function mergeDocuments($assetIds)
	{
		return $this->contentService->mergeDocuments($assetIds);
	}

	/**
	 * @param int $operationId
	 * @return \Rokolabs\ROKOMobi\Result\DocumentOperationStatus
	 */
	public function checkDocumentOperation($operationId)
	{
		return $this->contentService->checkDocumentOperation($operationId);
	}

	/**
	 * @return \Rokolabs\ROKOMobi\Result\AssetType
	 * @throws \Exception
	 */
	protected function getPrimaryAssetType()
	{
		$assetTypes = $this->contentService->getAssetTypes();

		$primaryAssetType = null;
		foreach ($assetTypes as $assetType) {
			if ($assetType->isPrimary()) {
				$primaryAssetType = $assetType;

				break;
			}
		}

		if (empty($primaryAssetType)) {
			throw new \Exception('Primary asset type doesn\'t exist');
		}

		return $primaryAssetType;
	}

	/**
	 * @return bool
	 */
	public static function isRemoteFileUploadEnabled()
	{
		//todo: temporary
		return false;

		$app = \Opake\Application::get();
		return ($app->config('app.documents.provider') === 'Rokomobi');
	}
}