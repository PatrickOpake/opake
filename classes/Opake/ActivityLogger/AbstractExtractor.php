<?php

namespace Opake\ActivityLogger;

abstract class AbstractExtractor
{
	/**
	 * @return AbstractExtractor
	 */
	public function createNewInstance()
	{
		return new static();
	}

	abstract public function extractArrays();
}