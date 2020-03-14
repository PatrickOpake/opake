<?php
namespace OpakeAdmin\Service\ASCX12\E277\Response\Segments\PatientDetails;

use OpakeAdmin\Service\ASCX12\AbstractResponseSegment;

class ServiceStatusTracking extends AbstractResponseSegment
{

	protected $code;

	protected $amount;

	protected $serviceLevelStatuses = [];

	/**
	 * @return array
	 */
	public function getServiceLevelStatuses()
	{
		return $this->serviceLevelStatuses;
	}

	/**
	 * @return mixed
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @return mixed
	 */
	public function getAmount()
	{
		return $this->amount;
	}


	public function parseNodes($data)
	{
		foreach ($data as $line) {
			if ($line[0] === 'SVC') {
				$code = $this->explodeComponents($line[1]);
				if (isset($code[1])) {
					$this->code = $code[1];
				}
				$this->amount = $line[2];
			}
			if ($line[0] === 'STC') {
				$serviceLevelStatus =  new ServiceLevelStatus();
				$serviceLevelStatus->parseNodes([$line]);
				$this->serviceLevelStatuses[] = $serviceLevelStatus;
			}
		}
	}
}