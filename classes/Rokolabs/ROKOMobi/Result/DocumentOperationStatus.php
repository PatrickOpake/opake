<?php

namespace Rokolabs\ROKOMobi\Result;

use Rokolabs\ROKOMobi\Helper\ResponseParser;

class DocumentOperationStatus
{
	/**
	 * @var int
	 */
	protected $objectId;

	/**
	 * @var array
	 */
	protected $sourceDocuments;

	/**
	 * @var CreateFile
	 */
	protected $resultFile;

	/**
	 * @var string
	 */
	protected $status;

	/**
	 * @var string
	 */
	protected $errorMessage;

	/**
	 * @var \DateTime
	 */
	protected $createDate;

	/**
	 * @var \DateTime
	 */
	protected $updateDate;

	/**
	 * @param $objectId
	 * @param $sourceDocuments
	 * @param $resultFile
	 * @param $status
	 * @param $errorMessage
	 * @param $createDate
	 * @param $updateDate
	 */
	public function __construct($objectId, $sourceDocuments, $resultFile, $status, $errorMessage, $createDate, $updateDate)
	{
		$this->objectId = $objectId;
		$this->sourceDocuments = $sourceDocuments;
		$this->resultFile = $resultFile;
		$this->status = $status;
		$this->errorMessage = $errorMessage;
		$this->createDate = $createDate;
		$this->updateDate = $updateDate;
	}


	/**
	 * @return mixed
	 */
	public function getObjectId()
	{
		return $this->objectId;
	}

	/**
	 * @return array
	 */
	public function getSourceDocuments()
	{
		return $this->sourceDocuments;
	}

	/**
	 * @return CreateFile
	 */
	public function getResultFile()
	{
		return $this->resultFile;
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return string
	 */
	public function getErrorMessage()
	{
		return $this->errorMessage;
	}

	/**
	 * @return \DateTime
	 */
	public function getCreateDate()
	{
		return $this->createDate;
	}

	/**
	 * @return \DateTime
	 */
	public function getUpdateDate()
	{
		return $this->updateDate;
	}

	/**
	 * @param \stdClass $data
	 * @return DocumentOperationBegin
	 */
	public static function parse($data)
	{
		return new DocumentOperationStatus(
			$data->objectId,
			$data->sourceDocuments,
			$data->resultFile ? CreateFile::parse($data->resultFile) : null,
			$data->status,
			$data->errorMessage,
			ResponseParser::parseDate($data->createDate),
			ResponseParser::parseDate($data->updateDate)
		);
	}
}
