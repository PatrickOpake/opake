<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E271\Response;


use Opake\Helper\StringHelper;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\N3;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\N4;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\NM1;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\PER;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\PRV;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Loop;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Util\StringQueue;

class BenefitResponseBenefitRelatedEntity extends Loop
{
	protected $relatedEntityName;
	protected $relatedEntityAddress;
	protected $relatedEntityCityStateZip;
	protected $relatedEntityContactInformations;
	protected $relatedProviderInformation;

	public function __construct($s)
	{
		$this->relatedEntityContactInformations = [];
		$stringQueue = new StringQueue($s);

		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'NM1')) {
			$this->relatedEntityName = new NM1($stringQueue->getNext());
		}
		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'N3')) {
			$this->relatedEntityAddress = new N3($stringQueue->getNext());
		}
		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'N4')) {
			$this->relatedEntityCityStateZip = new N4($stringQueue->getNext());
		}
		while ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'PER')) {
			$this->relatedEntityContactInformations[] = new PER($stringQueue->getNext());
		}
		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'PRV')) {
			$this->relatedProviderInformation = new PRV($stringQueue->getNext());
		}
	}

	public function loadDefinition()
	{
		$this->messagesDefinition = [];
		$this->messagesDefinition[] = $this->relatedEntityName;
		$this->messagesDefinition[] = $this->relatedEntityAddress;
		$this->messagesDefinition[] = $this->relatedEntityCityStateZip;
		foreach ($this->relatedEntityContactInformations as $item) {
			$this->messagesDefinition[] = $item;
		}
		$this->messagesDefinition[] = $this->relatedProviderInformation;
	}

	public function getRelatedEntityName()
	{
		return $this->relatedEntityName;
	}

	public function setRelatedEntityName(NM1 $relatedEntityName)
	{
		$this->relatedEntityName = $relatedEntityName;
	}

	public function getRelatedEntityAddress()
	{
		return $this->relatedEntityAddress;
	}

	public function setRelatedEntityAddress(N3 $relatedEntityAddress)
	{
		$this->relatedEntityAddress = $relatedEntityAddress;
	}

	public function getRelatedEntityCityStateZip()
	{
		return $this->relatedEntityCityStateZip;
	}

	public function setRelatedEntityCityStateZip(N4 $relatedEntityCityStateZip)
	{
		$this->relatedEntityCityStateZip = $relatedEntityCityStateZip;
	}

	public function getRelatedEntityContactInformations()
	{
		return $this->relatedEntityContactInformations;
	}

	public function setRelatedEntityContactInformations($relatedEntityContactInformations)
	{
		$this->relatedEntityContactInformations = $relatedEntityContactInformations;
	}

	public function getRelatedProviderInformation()
	{
		return $this->relatedProviderInformation;
	}

	public function setRelatedProviderInformation(PRV $relatedProviderInformation)
	{
		$this->relatedProviderInformation = $relatedProviderInformation;
	}

	public function toArray()
	{
		$relatedEntityContactInformations = [];
		foreach ($this->getRelatedEntityContactInformations() as $item) {
			$relatedEntityContactInformations[] = $item->toArray();
		}
		return [
			'relatedEntityName' => $this->getRelatedEntityName() ? $this->getRelatedEntityName()->toArray() : [],
			'relatedEntityAddress' => $this->getRelatedEntityAddress() ? $this->getRelatedEntityAddress()->toArray() : [],
			'relatedEntityCityStateZip' => $this->getRelatedEntityCityStateZip() ? $this->getRelatedEntityCityStateZip()->toArray() : [],
			'relatedEntityContactInformations' => $relatedEntityContactInformations,
			'relatedProviderInformation' => $this->getRelatedProviderInformation() ? $this->getRelatedProviderInformation()->toArray() : [],
		];
	}

}