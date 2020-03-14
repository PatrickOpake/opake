<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E271\Response;


use Opake\Helper\StringHelper;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\AAA;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\DMG;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\DTP;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\HI;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\HL;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\INS;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\MPI;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\N3;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\N4;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\NM1;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\PRV;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\REF;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Header\TRN;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Loop;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Util\SegmentString;
use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Util\StringQueue;

class BenefitResponseDependent extends Loop
{
	protected $hierarchicalLevel;
	protected $traceNumbers;
	protected $name;
	protected $additionalIdentifications;
	protected $address;
	protected $cityStateZip;
	protected $requestValidations;
	protected $providerInformation;
	protected $demographic;
	protected $relationship;
	protected $healthCareDiagnosisCode;
	protected $dates;
	protected $militaryPersonnelInformation;

	protected $eligibilities;

	public function __construct($s)
	{
		if (!$s) {
			throw new \Exception('Empty response');
		}

		$this->traceNumbers = [];
		$this->additionalIdentifications = [];
		$this->requestValidations = [];
		$this->dates = [];
		$this->eligibilities = [];

		$stringQueue = new StringQueue($s);
		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'HL')) {
			$this->hierarchicalLevel = new HL($stringQueue->getNext());
		}
		while ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'TRN')) {
			$this->traceNumbers[] = new TRN($stringQueue->getNext());
		}
		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'NM1')) {
			$this->name = new NM1($stringQueue->getNext());
		}
		while ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'REF')) {
			$this->additionalIdentifications[] = new REF($stringQueue->getNext());
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
		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'DMG')) {
			$this->demographic = new DMG($stringQueue->getNext());
		}
		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'INS')) {
			$this->relationship = new INS($stringQueue->getNext());
		}
		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'HI')) {
			$this->healthCareDiagnosisCode = new HI($stringQueue->getNext());
		}
		while ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'DTP')) {
			$this->dates[] = new DTP($stringQueue->getNext());
		}
		if ($stringQueue->hasNext() && StringHelper::startsWith($stringQueue->peekNext(), 'MPI')) {
			$this->militaryPersonnelInformation = new MPI($stringQueue->getNext());
		}

		$strBuilder = '';
		while ($stringQueue->hasNext()) {
			$strBuilder .= $stringQueue->getNext();
		}
		$splitArray = SegmentString::split($strBuilder, "EB");
		foreach ($splitArray as $str) {
			$this->eligibilities[] = new BenefitResponseEligibility($str);
		}
	}

	public function loadDefinition()
	{
		$this->messagesDefinition = [];

		$this->messagesDefinition[] = $this->hierarchicalLevel;
		foreach ($this->traceNumbers as $item) {
			$this->messagesDefinition[] = $item;
		}
		$this->messagesDefinition[] = $this->name;
		foreach ($this->additionalIdentifications as $item) {
			$this->messagesDefinition[] = $item;
		}
		$this->messagesDefinition[] = $this->address;
		$this->messagesDefinition[] = $this->cityStateZip;
		foreach ($this->requestValidations as $item) {
			$this->messagesDefinition[] = $item;
		}
		$this->messagesDefinition[] = $this->providerInformation;
		$this->messagesDefinition[] = $this->demographic;
		$this->messagesDefinition[] = $this->relationship;
		$this->messagesDefinition[] = $this->healthCareDiagnosisCode;
		foreach ($this->dates as $item) {
			$this->messagesDefinition[] = $item;
		}
		$this->messagesDefinition[] = $this->militaryPersonnelInformation;
		foreach ($this->eligibilities as $item) {
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

	public function getTraceNumbers()
	{
		return $this->traceNumbers;
	}

	public function setTraceNumbers($traceNumbers)
	{
		$this->traceNumbers = $traceNumbers;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName(NM1 $name)
	{
		$this->name = $name;
	}

	public function getAdditionalIdentifications()
	{
		return $this->additionalIdentifications;
	}

	public function setAdditionalIdentifications($additionalIdentifications)
	{
		$this->additionalIdentifications = $additionalIdentifications;
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

	public function getDemographic()
	{
		return $this->demographic;
	}

	public function setDemographic(DMG $demographic)
	{
		$this->demographic = $demographic;
	}

	public function getRelationship()
	{
		return $this->relationship;
	}

	public function setRelationship(INS $relationship)
	{
		$this->relationship = $relationship;
	}

	public function getHealthCareDiagnosisCode()
	{
		return $this->healthCareDiagnosisCode;
	}

	public function setHealthCareDiagnosisCode(HI $healthCareDiagnosisCode)
	{
		$this->healthCareDiagnosisCode = $healthCareDiagnosisCode;
	}

	public function getDates()
	{
		return $this->dates;
	}

	public function setDates($dates)
	{
		$this->dates = $dates;
	}

	public function getMilitaryPersonnelInformation()
	{
		return $this->militaryPersonnelInformation;
	}

	public function setMilitaryPersonnelInformation(MPI $militaryPersonnelInformation)
	{
		$this->militaryPersonnelInformation = $militaryPersonnelInformation;
	}

	public function getEligibilities()
	{
		return $this->eligibilities;
	}

	public function setEligibilities($eligibilities)
	{
		$this->eligibilities = $eligibilities;
	}

	public function toArray()
	{
		$traceNumbers = [];
		$additionalIdentifications = [];
		$requestValidations = [];
		$dates = [];
		$eligibilities = [];
		foreach ($this->getTraceNumbers() as $item) {
			$traceNumbers[] = $item->toArray();
		}
		foreach ($this->getAdditionalIdentifications() as $item) {
			$additionalIdentifications[] = $item->toArray();
		}
		foreach ($this->getRequestValidations() as $item) {
			$requestValidations[] = $item->toArray();
		}
		foreach ($this->getDates() as $item) {
			$dates[] = $item->toArray();
		}
		foreach ($this->getEligibilities() as $item) {
			$eligibilities[] = $item->toArray();
		}
		return [
			'hierarchicalLevel' => $this->getHierarchicalLevel() ? $this->getHierarchicalLevel()->toArray() : [],
			'traceNumbers' => $traceNumbers,
			'name' => $this->getName() ? $this->getName()->toArray() : [],
			'additionalIdentifications' => $additionalIdentifications,
			'address' => $this->getAddress() ? $this->getAddress()->toArray() : [],
			'cityStateZip' => $this->getCityStateZip() ? $this->getCityStateZip()->toArray() : [],
			'requestValidations' => $requestValidations,
			'providerInformation' => $this->getProviderInformation() ? $this->getProviderInformation()->toArray() : [],
			'demographic' => $this->getDemographic() ? $this->getDemographic()->toArray() : [],
			'relationship' => $this->getRelationship() ? $this->getRelationship()->toArray() : [],
			'healthCareDiagnosisCode' => $this->getHealthCareDiagnosisCode() ? $this->getHealthCareDiagnosisCode()->toArray() : [],
			'dates' => $dates,
			'militaryPersonnelInformation' => $this->getMilitaryPersonnelInformation() ? $this->getMilitaryPersonnelInformation()->toArray() : [],
			'eligibilities' => $eligibilities,
		];
	}

}