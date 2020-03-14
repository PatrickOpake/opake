<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E271\Response;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Blocks\Transaction;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Util\SegmentString;

class BenefitResponseTransaction extends Transaction
{
	protected $informationSources;

	public function __construct($transaction)
	{
		$this->transactionSetHeader = $transaction->getTransactionSetHeader();
		$this->beginningOfHierarchicalTransaction = $transaction->getBeginningOfHierarchicalTransaction();
		$this->transactionSetTrailer = $transaction->getTransactionSetTrailer();
		$this->content = $transaction->getContent();
		$this->informationSources = [];
		$this->parseContent();
	}

	protected function parse()
	{
		$envelope = $this->getInterchangeEnvelope();
		foreach ($envelope->getFunctionalGroups() as $group) {
			$transactions = $group->getTransactions();
			foreach ($transactions as $key => $transaction) {
				$transactions[$key] = new BenefitResponseTransaction($transactions[$key]);
			}
		}
	}

	protected function parseContent()
	{
		$sourceStrings = SegmentString::split($this->getContent(), "HL");
		$sources = SegmentString::joinLevel($sourceStrings);

		foreach ($sources as $str) {
			$this->informationSources[] = new BenefitResponseInformationSource($str);
		}
	}

	public function loadDefinition()
	{
		$this->messagesDefinition = [];
		$this->messagesDefinition[] = $this->getTransactionSetHeader();
		$this->messagesDefinition[] = $this->getBeginningOfHierarchicalTransaction();
		foreach ($this->informationSources as $item) {
			$this->messagesDefinition[] = $item;
		}
		$this->messagesDefinition[] = $this->getTransactionSetTrailer();
	}

	public function getInformationSources()
	{
		return $this->informationSources;
	}

	public function setInformationSources($informationSources)
	{
		$this->informationSources = $informationSources;
	}

}