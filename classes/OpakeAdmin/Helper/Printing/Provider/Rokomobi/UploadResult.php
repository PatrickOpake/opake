<?php

namespace OpakeAdmin\Helper\Printing\Provider\Rokomobi;

class UploadResult
{
	/**
	 * @var int
	 */
	protected $contentItemId;

	/**
	 * @var int
	 */
	protected $assetId;

	/**
	 * @param int $contentItemId
	 * @param int $assetId
	 */
	public function __construct($contentItemId, $assetId)
	{
		$this->contentItemId = $contentItemId;
		$this->assetId = $assetId;
	}


	/**
	 * @return int
	 */
	public function getContentItemId()
	{
		return $this->contentItemId;
	}

	/**
	 * @param int $contentItemId
	 */
	public function setContentItemId($contentItemId)
	{
		$this->contentItemId = $contentItemId;
	}

	/**
	 * @return int
	 */
	public function getAssetId()
	{
		return $this->assetId;
	}

	/**
	 * @param int $assetId
	 */
	public function setAssetId($assetId)
	{
		$this->assetId = $assetId;
	}
}