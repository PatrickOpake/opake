<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E271\Response;


use Opake\Helper\StringHelper;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\AAA;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\HL;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\N3;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\N4;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\NM1;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\PRV;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\REF;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Loop;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Util\SegmentString;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Util\StringQueue;

class BenefitResponseInformationReceiver extends Loop
{
	protected $hierarchicalLevel;
	protected $individualOrOrganizationalName;
	protected $referenceInformations;
	protected $address;
	protected $cityStateZip;
	protected $requestValidations;
	protected $providerInformation;

	protected $subscribers;

	public function __construct($s)
	{
		$this->referenceInformations = [];
		$stringQueue = new StringQueue($s);

		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'HL')) {
			$this->hierarchicalLevel = new HL($stringQueue->getNext());
		}
		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'NM1')) {
			$this->individualOrOrganizationalName = new NM1($stringQueue->getNext());
		}
		while ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'REF')) {
			$this->referenceInformations[] = new REF($stringQueue->getNext());
		}
		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'N3')) {
			$this->address = new N3($stringQueue->getNext());
		}
		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'N4')) {
			$this->cityStateZip = new N4($stringQueue->getNext());
		}
		while ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'AAA')) {
			$this->requestValidations[] = new AAA($stringQueue->getNext());
		}
		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'PRV')) {
			$this->providerInformation = new PRV($stringQueue->getNext());
		}

		$builder = [];
		$count = 0;
		while ($stringQueue->hasNext() ) {
			if(StringHelper::startsWith($stringQueue->peekNext(), 'HL')) {
				$hl = new HL($stringQueue->peekNext());
				if($hl->getHierarchicalLevelCode() == 22) {
					$count++;
					$builder[$count] = '';
				}
			}
			$builder[$count] .= $stringQueue->getNext();
		}
		foreach ($builder as $item) {
			$this->subscribers[] = new BenefitResponseSubscriber($item);
		}
	}


	function loadDefinition()
	{
		$this->messagesDefinition = [];
		$this->messagesDefinition[] = $this->hierarchicalLevel;
		$this->messagesDefinition[] = $this->individualOrOrganizationalName;
		foreach ($this->referenceInformations as $item) {
			$this->messagesDefinition[] = $item;
		}
		$this->messagesDefinition[] = $this->address;
		$this->messagesDefinition[] = $this->cityStateZip;
		$this->messagesDefinition[] = $this->providerInformation;
		foreach ($this->subscribers as $item) {
			$this->messagesDefinition[] = $item;
		}
	}


	public function getHierarchicalLevel()
	{
		return $this->hierarchicalLevel;
	}

	public function setHierarchicalLevel(HL $hierarchicalLevel)
	{
		$this->hierarchicalLevel = $hierarchicalLevel;
	}

	public function getIndividualOrOrganizationalName()
	{
		return $this->individualOrOrganizationalName;
	}

	public function setIndividualOrOrganizationalName(NM1 $individualOrOrganizationalName)
	{
		$this->individualOrOrganizationalName = $individualOrOrganizationalName;
	}

	public function getReferenceInformations()
	{
		return $this->referenceInformations;
	}

	public function setReferenceInformations($referenceInformations)
	{
		$this->referenceInformations = $referenceInformations;
	}

	public function getAddress()
	{
		return $this->address;
	}

	public function setAddress(N3 $address)
	{
		$this->address = $address;
	}

	public function getCityStateZip()
	{
		return $this->cityStateZip;
	}

	public function setCityStateZip(N4 $cityStateZip)
	{
		$this->cityStateZip = $cityStateZip;
	}

	public function getRequestValidations()
	{
		return $this->requestValidations;
	}

	public function setRequestValidations($requestValidations)
	{
		$this->requestValidations = $requestValidations;
	}

	public function getProviderInformation()
	{
		return $this->providerInformation;
	}

	public function setProviderInformation(PRV $providerInformation)
	{
		$this->providerInformation = $providerInformation;
	}

	public function getSubscribers()
	{
		return $this->subscribers;
	}

	public function setSubscribers($subscribers)
	{
		$this->subscribers = $subscribers;
	}

	public function toArray()
	{
		$referenceInformations = [];
		foreach ($this->getReferenceInformations() as $item) {
			$referenceInformations[] = $item->toArray();
		}
		return [
			'hierarchicalLevel' => $this->getHierarchicalLevel() ? $this->getHierarchicalLevel()->toArray() : [],
			'individualOrOrganizationalName' => $this->getIndividualOrOrganizationalName() ? $this->getIndividualOrOrganizationalName()->toArray() : [],
			'referenceInformations' => $referenceInformations,
			'address' => $this->getAddress() ? $this->getAddress()->toArray() : [],
			'cityStateZip' => $this->getCityStateZip() ? $this->getCityStateZip()->toArray() : [],
			'providerInformation' => $this->getProviderInformation() ? $this->getProviderInformation()->toArray() : [],
		];
	}
}