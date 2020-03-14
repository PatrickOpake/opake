<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility;


use OpakeAdmin\Service\ASCX12\HealthCareEligibility\Blocks\InterchangeEnvelope;

class X12Message extends Loop
{
	protected $interchangeEnvelope;

	public function __construct($s)
	{
		$this->interchangeEnvelope = new InterchangeEnvelope($s);
	}

	public function getInterChangeEnvelope()
	{
		return $this->interchangeEnvelope;
	}

	public function loadDefinition()
	{
		$this->messagesDefinition = [];
		$this->messagesDefinition[] = $this->interchangeEnvelope;
	}


}