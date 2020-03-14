<?php

namespace OpakeAdmin\Helper\Printing\Utils\Chart;

use OpakeAdmin\Helper\Printing\Document;
use OpakeAdmin\Helper\Printing\Document\Cases\Chart\ChartFileWithHeader;
use OpakeAdmin\Helper\Printing\Document\Cases\Chart\MultipleChartFileWithHeader;

class ChartFileWithHeaderOptimizer
{
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

	public function tryToOptimize()
	{
		$documents = $this->documents;

		$newDocuments = [];
		$currentGroup = [];
		foreach ($documents as $document) {
			if (!$document instanceof ChartFileWithHeader) {
				if ($currentGroup) {
					$newDocuments[] = $this->mergeFollowingForms($currentGroup);
				}
				$newDocuments[] = $document;
				$currentGroup = [];
				continue;
			}

			if (!$currentGroup || ($this->isCompatibleFormDocuments($currentGroup[0], $document))) {
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
			$multipleDocument = new MultipleChartFileWithHeader($usedForms, $case);
			return $multipleDocument;
		}
	}

	protected function isCompatibleFormDocuments($documentA, $documentB)
	{
		return ($documentA->getForm()->organization_id === $documentB->getForm()->organization_id);
	}
}