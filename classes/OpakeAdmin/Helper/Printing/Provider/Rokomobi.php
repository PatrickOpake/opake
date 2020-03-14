<?php

namespace OpakeAdmin\Helper\Printing\Provider;

use Opake\Helper\TimeFormat;
use Opake\Model\UploadedFile;
use OpakeAdmin\Helper\Printing\Provider\Rokomobi\Uploader;
use OpakeAdmin\Helper\Printing\Provider\Rokomobi\Uploader\ChartUploader;

class Rokomobi extends AbstractProvider
{

	/**
	 * @var \OpakeAdmin\Helper\RemoteDocument\Service
	 */
	protected $service;

	/**
	 * @param \OpakeAdmin\Helper\Printing\Document[] $documents
	 */
	public function compile($documents)
	{
		if (count($documents) == 1) {
			$document = reset($documents);
			return $this->compileSingleDocument($document);
		} else {
			return $this->compileMultipleDocuments($documents);
		}
	}

	/**
	 * @param \OpakeAdmin\Helper\Printing\Document $document
	 * @return \Rokolabs\ROKOMobi\Result\GetContentItem
	 */
	protected function uploadDocument($document)
	{
		$uploader = $this->getUploader($document);
		return $uploader->upload();
	}

	/**
	 * @param $document
	 * @return Uploader
	 */
	protected function getUploader($document)
	{
		if ($document instanceof \OpakeAdmin\Helper\Printing\Document\Cases\Chart) {
			return new ChartUploader($document, $this->service);
		}

		return new Uploader($document, $this->service);
	}

	/**
	 * @param string $url
	 * @return mixed
	 * @throws \Exception
	 */
	protected function downloadFile($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$data = curl_exec($ch);
		$error = curl_error($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);

		if ($error) {
			throw new \Exception('Curl error: ' . $error);
		}

		if ($httpCode != 200) {
			throw new \Exception('Bad response HTTP code: ' . $httpCode);
		}

		return $data;
	}

	protected function compileSingleDocument($document)
	{

	}

	protected function compileMultipleDocuments($documents)
	{

	}

	/**
	 * @param \Rokolabs\ROKOMobi\Result\GetContentItem $remoteFileResponse
	 * @return \Opake\Model\RemoteStorageDocument
	 */
	protected function createRemoteFileRecord($remoteFileResponse)
	{
		$model = $this->pixie->orm->get('RemoteStorageDocument');
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

		$model = $this->pixie->orm->get('Document_PrintResult_CleaningQueueRecord');
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

