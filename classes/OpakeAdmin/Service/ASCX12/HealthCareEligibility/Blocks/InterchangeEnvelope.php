<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Blocks;


use Opake\Helper\StringHelper;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\IEA;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\ISA;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Loop;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Util\StringQueue;

class InterchangeEnvelope extends Loop
{
	protected $transactionSetHeader;
	protected $functionalGroups;
	protected $transactionSetTrailer;

	public function __construct($s)
	{
		$stringQueue = new StringQueue($s);
		$this->functionalGroups = [];

		$builder = null;
		while ($stringQueue->hasNext()) {
			$next = trim($stringQueue->getNext());
			if (StringHelper::startsWith($next, 'ISA')) {
				$this->transactionSetHeader = new ISA($next);
			} else if (StringHelper::startsWith($next, 'IEA')) {
				$this->transactionSetTrailer = new IEA($next);
			} else if (StringHelper::startsWith($next, 'GS')) {
				$builder = '';
				$builder .= $next;
			} else if (StringHelper::startsWith($next, 'GE')) {
				if ($builder != null) {
					$builder .= $next;
					$groupContent = $builder;
					$group = new FunctionalGroup($groupContent);
					$this->functionalGroups[] = $group;
				}
			} else {
				if ($builder != null) $builder .= $next;
			}
		}
	}

	public function loadDefinition()
	{
		$this->messagesDefinition = [];
		$this->messagesDefinition[] = $this->transactionSetHeader;
		foreach ($this->messagesDefinition as $item) {
			$this->messagesDefinition[] = $item;
		}
		$this->messagesDefinition[] = $this->transactionSetTrailer;
	}

	public function getTransactionSetHeader()
	{
		return $this->transactionSetHeader;
	}

	public function getTransactionSetTrailer()
	{
		return $this->transactionSetTrailer;
	}

	public function getFunctionalGroups()
	{
		return $this->functionalGroups;
	}

}