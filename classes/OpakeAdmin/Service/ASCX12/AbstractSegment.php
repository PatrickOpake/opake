<?php

namespace OpakeAdmin\Service\ASCX12;

abstract class AbstractSegment
{

	/**
	 * @var AbstractSegment[]
	 */
	protected $childSegments = [];

	/**
	 *
	 */
	public function resetChildSegments()
	{
		$this->childSegments = [];
	}

	/**
	 * @param AbstractSegment $segment
	 */
	public function addChildSegment(AbstractSegment $segment)
	{
		$this->childSegments[] = $segment;
	}

	/**
	 * @return AbstractSegment[]
	 */
	public function getChildSegments()
	{
		return $this->childSegments;
	}

	/**
	 * @return AbstractSegment
	 * @throws \Exception
	 */
	public function getFirstChildSegment()
	{
		if (!$this->childSegments) {
			throw new \Exception('The segment has no children segments');
		}
		return $this->childSegments[0];
	}

	/**
	 * @return bool
	 */
	public function hasChildSegments()
	{
		return (bool) $this->childSegments;
	}

	/**
	 * @param bool $includeChildren
	 * @return int
	 */
	public function getCountOfChildSegments($includeChildren = false)
	{
		if (!$includeChildren) {
			return count($this->childSegments);
		}

		$count = 0;
		$countFunc = function($segment) use ($count, &$countFunc) {
			if ($segment->getChildSegments()) {
				foreach ($segment->getChildSegments() as $childSegment) {
					$count++;
					$countFunc($childSegment);
				}
			}
		};

		$countFunc($this);

		return $count;
	}
}
