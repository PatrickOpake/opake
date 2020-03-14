<?php

namespace OpakeAdmin\Helper\Printing\Utils\Chart;

use OpakeAdmin\Helper\Printing\Document;
use OpakeAdmin\Helper\Printing\Document\Cases\Chart\ChartOwnText;
use OpakeAdmin\Helper\Printing\Document\Cases\Chart\MultipleChartOwnText;

class ChartOwnTextOptimizer
{
	/**
	 * @var int
	 */
	protected $multipleFormsMaximumCount = 6;

	/**
	 * @var Document[]
	 */
	protected $documents;

	/**
	 * @param Document[] $documents
	 */
	public function __construct($documents)
	{
		$this->documents = $documents;
	}

	/**
	 * @return int
	 */
	public function getMultipleFormsMaximumCount()
	{
		return $this->multipleFormsMaximumCount;
	}

	/**
	 * @param int $multipleFormsMaximumCount
	 */
	public function setMultipleFormsMaximumCount($multipleFormsMaximumCount)
	{
		$this->multipleFormsMaximumCount = $multipleFormsMaximumCount;
	}

	public function tryToOptimize()
	{
		$documents = $this->documents;

		$newDocuments = [];
		$currentGroup = [];
		foreach ($documents as $document) {
			if (!$document instanceof ChartOwnText) {
				if ($currentGroup) {
					$newDocuments[] = $this->mergeFollowingForms($currentGroup);
				}
				$newDocuments[] = $document;
				$currentGroup = [];
				continue;
			}

			if (!$currentGroup || ($this->isCompatibleFormDocuments($currentGroup[0], $document) &&
					count($currentGroup) <= $this->multipleFormsMaximumCount)) {

				$currentGroup[] = $document;

			} else {
				$newDocuments[] = $this->mergeFollowingForms($currentGroup);
				$currentGroup = [$document];
			}
		}

		if ($currentGroup) {
			$newDocuments[] = $this->mergeFollowingForms($currentGroup);
		}

		return $newDocuments;
	}

	protected function mergeFollowingForms($group)
	{
		if (count($group) == 1) {
			return $group[0];
		} else {
			$usedForms = [];
			$case = null;
			foreach ($group as $groupDocument) {
				$usedForms[] = $groupDocument->getForm();
				$case = $groupDocument->getCase();
			}
			$multipleDocument = new MultipleChartOwnText($usedForms, $case);
			return $multipleDocument;
		}
	}

	protected function isCompatibleFormDocuments($documentA, $documentB)
	{
		return ($documentA->getForm()->is_landscape === $documentB->getForm()->is_landscape &&
			$documentA->getForm()->include_header === $documentB->getForm()->include_header);
	}
}