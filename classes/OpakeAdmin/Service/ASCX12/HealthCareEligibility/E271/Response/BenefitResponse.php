<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E271\Response;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\X12Message;

class BenefitResponse extends X12Message
{
	public function __construct($s)
	{
		parent::__construct($s);
		$this->parse();
	}

	protected function parse()
	{
		$envelope = $this->getInterchangeEnvelope();
		foreach ($envelope->getFunctionalGroups() as $group) {
			$transactions = $group->getTransactions();
			foreach ($transactions as $key => $transaction) {
				$transactions[$key] = new BenefitResponseTransaction($transaction);
			}
			$group->setTransactions($transactions);
		}
	}

}