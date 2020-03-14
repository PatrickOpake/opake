<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility;


abstract class Loop
{
	protected $messagesDefinition;

	public function __construct()
	{
		$this->messagesDefinition = [];
	}

	abstract function loadDefinition();

	public function validate() {
		if (count($this->messagesDefinition) == 0) {
			$this->loadDefinition();
		}

		foreach ($this->messagesDefinition as $message) {
			if(!$message->validate()) {
				return false;
			}
		}
		return true;
	}

	public function isEmpty()
	{
		if (count($this->messagesDefinition) == 0) {
			$this->loadDefinition();
		}

		foreach ($this->messagesDefinition as $message) {
			if(!$message->isEmpty()) {
				return false;
			}
		}
		return true;
	}

	public function toX12String()
	{
		if (count($this->messagesDefinition) == 0) {
			$this->loadDefinition();
		}

		$builder = '';
		foreach ($this->messagesDefinition as $message) {
			$builder .= (String)$message;
		}
		return $builder;
	}

	public function __toString()
	{
		return $this->toX12String();
	}


}