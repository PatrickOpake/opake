<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility;

use OpakeAdmin\Service\ASCX12\HealthCareEligibility\E271\Response\BenefitResponse;

class Parser
{
	/**
	 * @var \OpakeAdmin\Service\ASCX12\HealthCareEligibility\E271\Response\BenefitResponse
	 */
	protected $benefit;

	protected $x12;

	public function __construct($x12)
	{
		$this->x12 = $x12;
		if(!empty($this->x12)) {
			$this->parse();
		}
	}

	public function getBenefit()
	{
		return $this->benefit;
	}

	public function toArray()
	{
		$result = [];
		$beginTransaction = $this->benefit->getBeginningOfHierarchicalTransaction();
		$info = $this->benefit->getInformationSources();
		$receivers = $info[0]->getInformationReceivers();
		$subscribers = $receivers[0]->getSubscribers();
		$dependents = $subscribers[0]->getDependents();
		if(!empty($info[0])) {
			$result['info_source_detail'][] = $info[0]->toArray();
		}
		if(!empty($receivers[0])) {
			$result['info_receiver_detail'][] = $receivers[0]->toArray();
		}
		if(!empty($subscribers[0])) {
			$result['subscriber'][] = $subscribers[0]->toArray();
		}
		if(!empty($dependents[0])) {
			$result['dependent'][] = $dependents[0]->toArray();
		}
		if(!empty($beginTransaction)) {
			$result['beginTransaction'][] = $beginTransaction->toArray();
		}
		return $result;
	}

	public function toBatchArray()
	{
		$result = [];
		$beginTransaction = $this->benefit->getBeginningOfHierarchicalTransaction();
		$info = $this->benefit->getInformationSources();
		foreach ($info as $key => $infoSource) {
			$result['info_source_detail'][] = $infoSource->toArray();
			foreach ($infoSource->getInformationReceivers() as $keyReceiver =>  $infoReceiver) {
				$result['info_source_detail'][$key]['info_receiver_detail'][] = $infoReceiver->toArray();
				foreach ($infoReceiver->getSubscribers() as $keySubscriber => $subscriber) {
					$result['info_source_detail'][$key]['info_receiver_detail'][$keyReceiver]['subscriber'][] = $subscriber->toArray();
					foreach ($subscriber->getDependents() as $keyDependent => $dependent) {
						$result['info_source_detail'][$key]['info_receiver_detail'][$keyReceiver]['subscriber'][$keySubscriber]['dependent'][] = $dependent->toArray();
					}
				}
			}

		}
		if(!empty($beginTransaction)) {
			$result['beginTransaction'][] = $beginTransaction->toArray();
		}
		return $result;
	}

	protected function parse()
	{
		$benefitResponse = new BenefitResponse($this->x12);
		$functionalGroup = $benefitResponse->getInterChangeEnvelope()->getFunctionalGroups();
		$transactions = $functionalGroup[0]->getTransactions();
		$this->benefit = $transactions[0];
	}

}