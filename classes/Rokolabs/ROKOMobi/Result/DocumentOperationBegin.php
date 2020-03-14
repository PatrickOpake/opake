<?php

namespace Rokolabs\ROKOMobi\Result;

class DocumentOperationBegin
{
	protected $objectId;

	/**
	 * DocumentOperationBegin constructor.
	 * @param $objectId
	 */
	public function __construct($objectId)
	{
		$this->objectId = $objectId;
	}

	/**
	 * @return mixed
	 */
	public function getObjectId()
	{
		return $this->objectId;
	}

	/**
	 * @param \stdClass $data
	 * @return DocumentOperationBegin
	 */
	public static function parse($data)
	{
		return new DocumentOperationBegin($data->objectId);
	}
}
