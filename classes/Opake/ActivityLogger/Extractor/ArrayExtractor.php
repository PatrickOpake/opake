<?php

namespace Opake\ActivityLogger\Extractor;

use Opake\ActivityLogger\AbstractExtractor;
use Opake\Model\AbstractModel;

class ArrayExtractor extends AbstractExtractor
{
	/**
	 * @var array
	 */
	protected $newArray = [];

	/**
	 * @var array
	 */
	protected $oldArray = [];

	/**
	 * @param $array
	 */
	public function setArray($array)
	{
		$this->newArray = $array;
	}

	/**
	 * @param $newArray
	 * @param $oldArray
	 */
	public function setNewAndOldArrays($newArray, $oldArray)
	{
		$this->newArray = $newArray;
		$this->oldArray = $oldArray;
	}

	/**
	 * @return array
	 */
	public function getArray()
	{
		return $this->newArray;
	}

	/**
	 * @return array
	 */
	public function getOldArray()
	{
		return $this->oldArray;
	}

	/**
	 * @return array
	 */
	public function extractArrays()
	{
		return [$this->newArray, $this->oldArray];
	}
}