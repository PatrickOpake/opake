<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E271\Response;


use Opake\Helper\StringHelper;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\AAA;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\DTP;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\EB;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\HSD;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\III;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\LE;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\LS;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\MSG;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\REF;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Loop;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Util\SegmentString;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Util\StringQueue;

class BenefitResponseEligibility extends Loop
{
	protected $eligibility;
	protected $healthCareServiceDeliveries;
	protected $additionalInformations;
	protected $eligibilityDates;
	protected $requestValidations;
	protected $messageTexts;
	protected $additionalEligibilities;

	protected $loopHeader;
	protected $relatedEntities;
	protected $loopTrailer;

	public function __construct($s)
	{
		$this->healthCareServiceDeliveries = [];
		$this->additionalInformations = [];
		$this->eligibilityDates = [];
		$this->requestValidations = [];
		$this->messageTexts = [];
		$this->additionalEligibilities = [];
		$this->relatedEntities = [];

		$stringQueue = new StringQueue($s);

		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'EB')) {
			$this->eligibility = new EB($stringQueue->getNext());
		}
		while ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'HSD')) {
			$this->healthCareServiceDeliveries[] = new HSD($stringQueue->getNext());
		}
		while ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'REF')) {
			$this->additionalInformations[] = new REF($stringQueue->getNext());
		}
		while ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'DTP')) {
			$this->eligibilityDates[] = new DTP($stringQueue->getNext());
		}
		while ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'AAA')) {
			$this->requestValidations[] = new AAA($stringQueue->getNext());
		}
		while ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'MSG')) {
			$this->messageTexts[] = new MSG($stringQueue->getNext());
		}
		while ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'III')) {
			$this->additionalEligibilities[] = new III($stringQueue->getNext());
		}

		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'LS')) {
			$this->loopHeader = new LS($stringQueue->getNext());
		}

		$relatedEntityBuilder = '';
		while ($stringQueue->hasNext() && !StringHelper::startsWith($stringQueue->peekNext(), 'LE')) {
			$relatedEntityBuilder .= $stringQueue->getNext();
		}

		$splitArray = SegmentString::split($relatedEntityBuilder, "NM1");
		foreach ($splitArray as $relatedStr) {
			$this->relatedEntities[] = new BenefitResponseBenefitRelatedEntity($relatedStr);
		}

		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'LE')) {
			$this->loopTrailer = new LE($stringQueue->getNext());
		}
	}

	function loadDefinition()
	{
		$this->messagesDefinition = [];

		$this->messagesDefinition[] = $this->eligibility;
		foreach ($this->healthCareServiceDeliveries as $item) {
			$this->messagesDefinition[] = $item;
		}
		foreach ($this->additionalInformations as $item) {
			$this->messagesDefinition[] = $item;
		}
		foreach ($this->eligibilityDates as $item) {
			$this->messagesDefinition[] = $item;
		}
		foreach ($this->requestValidations as $item) {
			$this->messagesDefinition[] = $item;
		}
		foreach ($this->messageTexts as $item) {
			$this->messagesDefinition[] = $item;
		}
		foreach ($this->additionalEligibilities as $item) {
			$this->messagesDefinition[] = $item;
		}

		$this->messagesDefinition[] = $this->loopHeader;
		foreach ($this->relatedEntities as $item) {
			$this->messagesDefinition[] = $item;
		}
		$this->messagesDefinition[] = $this->loopTrailer;
	}

	public function getEligibility()
	{
		return $this->eligibility;
	}

	public function setEligibility(EB $eligibility)
	{
		$this->eligibility = $eligibility;
	}

	public function getHealthCareServiceDeliveries()
	{
		return $this->healthCareServiceDeliveries;
	}

	public function setHealthCareServiceDeliveries($healthCareServiceDeliveries)
	{
		$this->healthCareServiceDeliveries = $healthCareServiceDeliveries;
	}

	public function getAdditionalInformations()
	{
		return $this->additionalInformations;
	}

	public function setAdditionalInformations($additionalInformations)
	{
		$this->additionalInformations = $additionalInformations;
	}

	public function getEligibilityDates()
	{
		return $this->eligibilityDates;
	}

	public function setEligibilityDates($eligibilityDates)
	{
		$this->eligibilityDates = $eligibilityDates;
	}

	public function getRequestValidations()
	{
		return $this->requestValidations;
	}

	public function setRequestValidations($requestValidations)
	{
		$this->requestValidations = $requestValidations;
	}

	public function getMessageTexts()
	{
		return $this->messageTexts;
	}

	public function setMessageTexts($messageTexts)
	{
		$this->messageTexts = $messageTexts;
	}

	public function getAdditionalEligibilities()
	{
		return $this->additionalEligibilities;
	}

	public function setAdditionalEligibilities($additionalEligibilities)
	{
		$this->additionalEligibilities = $additionalEligibilities;
	}

	public function getLoopHeader()
	{
		return $this->loopHeader;
	}

	public function setLoopHeader(LS $loopHeader)
	{
		$this->loopHeader = $loopHeader;
	}

	public function getRelatedEntities()
	{
		return $this->relatedEntities;
	}

	public function setRelatedEntities($relatedEntities)
	{
		$this->relatedEntities = $relatedEntities;
	}

	public function getLoopTrailer()
	{
		return $this->loopTrailer;
	}

	public function setLoopTrailer(LE $loopTrailer)
	{
		$this->loopTrailer = $loopTrailer;
	}

	public function toArray()
	{
		$healthCareServiceDeliveries = [];
		$additionalInformations = [];
		$eligibilityDates = [];
		$requestValidations = [];
		$messageTexts = [];
		$additionalEligibilities = [];
		$relatedEntities = [];

		foreach ($this->getHealthCareServiceDeliveries() as $item) {
			$healthCareServiceDeliveries[] = $item->toArray();
		}
		foreach ($this->getAdditionalInformations() as $item) {
			$additionalInformations[] = $item->toArray();
		}
		foreach ($this->getEligibilityDates() as $item) {
			$eligibilityDates[] = $item->toArray();
		}
		foreach ($this->getRequestValidations() as $item) {
			$requestValidations[] = $item->toArray();
		}
		foreach ($this->getMessageTexts() as $item) {
			$messageTexts[] = $item->toArray();
		}
		foreach ($this->getAdditionalEligibilities() as $item) {
			$additionalEligibilities[] = $item->toArray();
		}
		foreach ($this->relatedEntities as $item) {
			$relatedEntities[] = $item->toArray();
		}

		return [
			'eligibility' => $this->getEligibility() ? $this->getEligibility()->toArray() : [],
			'healthCareServiceDeliveries' => $healthCareServiceDeliveries,
			'additionalInformations' => $additionalInformations,
			'eligibilityDates' => $eligibilityDates,
			'requestValidations' => $requestValidations,
			'messageTexts' => $messageTexts,
			'additionalEligibilities' => $additionalEligibilities,
			'loopHeader' => $this->getLoopHeader() ? $this->getLoopHeader()->toArray() : [],
			'relatedEntities' => $relatedEntities,
			'loopTrailer' => $this->getLoopTrailer() ? $this->getLoopTrailer()->toArray() : [],
		];
	}
}