<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\Blocks;


use Opake\Helper\StringHelper;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\BHT;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\SE;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\ST;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Loop;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Util\StringQueue;

class Transaction extends Loop
{
	protected $transactionSetHeader;
	protected $beginningOfHierarchicalTransaction;
	protected $transactionSetTrailer;
	protected $content;

	public function __construct($s)
	{
		$stringQueue = new StringQueue($s);
		$builder = '';

		while ($stringQueue->hasNext()) {
			$next = trim($stringQueue->getNext());
			if (StringHelper::startsWith($next, 'ST')) {
				$this->transactionSetHeader = new ST($next);
			} else if (StringHelper::startsWith($next, 'BHT')) {
				$this->beginningOfHierarchicalTransaction = new BHT($next);
			} else if (StringHelper::startsWith($next, 'SE')) {
				$this->transactionSetTrailer = new SE($next);
			} else {
				$builder .= $next;
			}
		}
		$this->content = $builder;
	}

	public function loadDefinition()
	{
		$this->messagesDefinition = [];
		$this->messagesDefinition[] = $this->transactionSetHeader;
		$this->messagesDefinition[] = $this->beginningOfHierarchicalTransaction;
		$this->messagesDefinition[] = $this->transactionSetTrailer;
	}

	public function getContent()
	{
		return $this->content;
	}

	public function getTransactionSetHeader()
	{
		return $this->transactionSetHeader;
	}

	public function getBeginningOfHierarchicalTransaction()
	{
		return $this->beginningOfHierarchicalTransaction;
	}

	public function getTransactionSetTrailer()
	{
		return $this->transactionSetTrailer;
	}

}