<?php

namespace Opake\ActivityLogger\Action;

use Opake\ActivityLogger\AbstractAction;
use Opake\ActivityLogger\Extractor\ArrayExtractor;

class ArrayAction extends AbstractAction
{

	/**
	 * @param array $array
	 * @return $this
	 */
	public function setArray($array)
	{
		$this->getExtractor()->setArray($array);

		return $this;
	}

	/**
	 * @param array $newArray
	 * @param array $oldArray
	 * @return $this
	 */
	public function setNewAndOldArrays($newArray, $oldArray)
	{
		$this->getExtractor()->setNewAndOldArrays($newArray, $oldArray);

		return $this;
	}

	/**
	 * @return ArrayExtractor
	 */
	public function getExtractor()
	{
		return parent::getExtractor();
	}

	/**
	 * @return ArrayExtractor
	 */
	protected function createExtractor()
	{
		return new ArrayExtractor();
	}
}