<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E271\Response;


use Opake\Helper\StringHelper;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\AAA;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\HL;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\NM1;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\PER;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Loop;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Util\StringQueue;

class BenefitResponseInformationSource extends Loop
{
	public $hierarchicalLevel;
	public $requestValidation;
	public $individualOrOrganizationalName;
	public $contactInformation;
	public $requestValidation2;

	public $informationReceivers;

	public function __construct($s)
	{
		$this->requestValidation = [];
		$this->contactInformation = [];
		$this->requestValidation2 = [];
		$this->informationReceivers = [];
		$stringQueue = new StringQueue($s);

		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'HL')) {
			$this->hierarchicalLevel = new HL($stringQueue->getNext());
		}
		while ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'AAA')) {
			$this->requestValidation[] = new AAA($stringQueue->getNext());
		}
		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'NM1')) {
			$this->individualOrOrganizationalName = new NM1($stringQueue->getNext());
		}
		while ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'PER')) {
			$this->contactInformation[] = new PER($stringQueue->getNext());
		}
		while ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'AAA')) {
			$this->requestValidation2[] = new AAA($stringQueue->getNext());
		}

		$receiverString = '';
		while ($stringQueue->hasNext()) {
			$receiverString .= $stringQueue->getNext();
		}
		$this->informationReceivers[] = new BenefitResponseInformationReceiver($receiverString);
	}

	public function loadDefinition()
	{
		$this->messagesDefinition = [];
		$this->messagesDefinition[] = $this->hierarchicalLevel;
		foreach ($this->requestValidation as $item) {
			$this->messagesDefinition[] = $item;
		}
		$this->messagesDefinition[] = $this->individualOrOrganizationalName;
		foreach ($this->contactInformation as $item) {
			$this->messagesDefinition[] = $item;
		}
		foreach ($this->requestValidation2 as $item) {
			$this->messagesDefinition[] = $item;
		}
		foreach ($this->informationReceivers as $item) {
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

	public function getInformationReceivers()
	{
		return $this->informationReceivers;
	}

	public function setInformationReceivers($informationReceivers)
	{
		$this->informationReceivers = $informationReceivers;
	}

	public function getRequestValidation()
	{
		return $this->requestValidation;
	}

	public function setRequestValidation($requestValidation)
	{
		$this->requestValidation = $requestValidation;
	}

	public function getContactInformation()
	{
		return $this->contactInformation;
	}

	public function setContactInformation($contactInformation)
	{
		$this->contactInformation = $contactInformation;
	}

	public function getRequestValidation2()
	{
		return $this->requestValidation2;
	}

	public function setRequestValidation2($requestValidation2)
	{
		$this->requestValidation2 = $requestValidation2;
	}

	public function toArray()
	{
		$requestValidation = [];
		$contactInformation = [];
		$requestValidation2 = [];
		foreach ($this->getRequestValidation() as $item) {
			$requestValidation[] = $item->toArray();
		}
		foreach ($this->contactInformation as $item) {
			$contactInformation[] = $item->toArray();
		}
		foreach ($this->requestValidation2 as $item) {
			$requestValidation2[] = $item->toArray();
		}
		return [
			'hierarchicalLevel' => $this->getHierarchicalLevel() ? $this->getHierarchicalLevel()->toArray() : [],
			'requestValidation' => $requestValidation,
			'individualOrOrganizationalName' => $this->getIndividualOrOrganizationalName() ? $this->getIndividualOrOrganizationalName()->toArray() : [],
			'contactInformation' => $contactInformation,
			'requestValidation2' => $requestValidation2,
		];
	}
}