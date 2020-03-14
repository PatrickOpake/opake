<?php

namespace OpakeAdmin\Helper\Printing\Provider\Rokomobi;

use Opake\Application;
use Opake\Helper\TimeFormat;

class Uploader
{
	/**
	 * @var \OpakeAdmin\Helper\Printing\Document
	 */
	protected $document;

	/**
	 * @var  \OpakeAdmin\Helper\RemoteDocument\Service
	 */
	protected $service;

	/**
	 * @param $document
	 * @param $service
	 */
	public function __construct($document, $service)
	{
		$this->document = $document;
		$this->service = $service;
	}

	/**
	 * @return UploadResult
	 * @throws \Exception
	 */
	public function upload()
	{
		$app = Application::get();
		$document = $this->document;

		$content = $document->getContent();
		$tmpPath = $app->app_dir . '/_tmp/' . uniqid();

		file_put_contents($tmpPath, $content);

		$filename = $document->getFileName();

		$contentItem = $this->service->uploadFile($filename, $tmpPath, $document->getContentMimeType(), false);

		if (is_file($tmpPath)) {
			unlink($tmpPath);
		}

		$remoteFile = $this->createRemoteFileRecord($contentItem);
		$this->addRemoteFileToCleaningQueue($remoteFile);

		return new UploadResult($contentItem->getObjectId(), $contentItem->getFirstAsset()->getObjectId());
	}

	/**
	 * @param \Rokolabs\ROKOMobi\Result\GetContentItem $remoteFileResponse
	 * @return \Opake\Model\RemoteStorageDocument
	 */
	protected function createRemoteFileRecord($remoteFileResponse)
	{
		$app = Application::get();
		$model =$app->orm->get('RemoteStorageDocument');
		$model->filename = $remoteFileResponse->getName();
		$model->content_item_id = $remoteFileResponse->getObjectId();
		$model->asset_id = $remoteFileResponse->getFirstAsset()->getObjectId();

		$model->save();

		return $model;
	}


	/**
	 * @param \Opake\Model\RemoteStorageDocument $remoteFile
	 * @return \Opake\Model\AbstractModel
	 */
	protected function addRemoteFileToCleaningQueue($remoteFile)
	{
		$currentDate = new \DateTime();

		$app = Application::get();
		$model = $app->orm->get('Document_PrintResult_CleaningQueueRecord');
		$model->remote_file_id = $remoteFile->id();
		$model->added_date = TimeFormat::formatToDBDatetime($currentDate);
		$model->save();

		return $model;
	}

	/**
	 * @param \Opake\Model\UploadedFile $uploadedFile
	 * @return \Opake\Model\AbstractModel
	 */
	protected function addUploadedFileToCleaningQueue($uploadedFile)
	{
		$currentDate = new \DateTime();

		$model = $this->pixie->orm->get('Document_PrintResult_CleaningQueueRecord');
		$model->uploaded_file_id = $uploadedFile->id();
		$model->added_date = TimeFormat::formatToDBDatetime($currentDate);
		$model->save();

		return $model;
	}
}