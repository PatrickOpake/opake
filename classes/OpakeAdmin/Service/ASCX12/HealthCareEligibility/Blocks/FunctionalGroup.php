<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Blocks;


use Opake\Helper\StringHelper;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\GE;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\GS;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Loop;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Util\StringQueue;

class FunctionalGroup extends Loop
{
	protected $functionalGroupHeader;
	protected $transactions;
	protected $functionalGroupTrailer;

	public function __construct($s)
	{
		$stringQueue = new StringQueue($s);
		$this->transactions = [];

		$builder = null;
		while ($stringQueue->hasNext()) {
			$next = trim($stringQueue->getNext());
			if (StringHelper::startsWith($next, 'GS')) {
				$this->functionalGroupHeader = new GS($next);
			} else if (StringHelper::startsWith($next, 'GE')) {
				$this->functionalGroupTrailer = new GE($next);
			} else if (StringHelper::startsWith($next, 'ST')) {
				$builder = '';
				$builder .= $next;
			} else if (StringHelper::startsWith($next, 'SE')) {
				if ($builder != null) {
					$builder .= $next;
					$groupContent = $builder;
					$transaction = new Transaction($groupContent);
					$this->transactions[] = $transaction;
				}
			} else {
				if ($builder != null) {
					$builder .= $next;
				}
			}
		}
	}

	public function loadDefinition()
	{
		$this->messagesDefinition = [];
		$this->messagesDefinition[] = $this->functionalGroupHeader;
		foreach ($this->transactions as $item) {
			$this->messagesDefinition[] = $item;
		}
		$this->messagesDefinition[] = $this->functionalGroupTrailer;
	}

	public function getFunctionalGroupHeader()
	{
		return $this->functionalGroupHeader;
	}

	public function getTransactions()
	{
		return $this->transactions;
	}

	public function setTransactions($t) {
		$this->transactions = $t;
	}

	public function getFunctionalGroupTrailer()
	{
		return $this->functionalGroupTrailer;
	}

}