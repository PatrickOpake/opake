<?php

namespace OpakeAdmin\Service\ASCX12\E277\Response\Segments\PatientDetails;

use OpakeAdmin\Service\ASCX12\AbstractResponseSegment;

class ClaimStatusTracking extends AbstractResponseSegment
{
	protected $referenceIdentifier;

	protected $claimLevelStatuses = [];

	/**
	 * @return mixed
	 */
	public function getReferenceIdentifier()
	{
		return $this->referenceIdentifier;
	}

	/**
	 * @return array
	 */
	public function getClaimLevelStatuses()
	{
		return $this->claimLevelStatuses;
	}


	public function parseNodes($data)
	{
		foreach ($data as $line) {
			if ($line[0] === 'TRN') {
				$this->referenceIdentifier = $line[2];
			}
			if ($line[0] === 'STC') {
				$claimLevelStatus = new ClaimLevelStatus();
				$claimLevelStatus->parseNodes([$line]);
				$this->claimLevelStatuses[] = $claimLevelStatus;
			}
		}
	}
}