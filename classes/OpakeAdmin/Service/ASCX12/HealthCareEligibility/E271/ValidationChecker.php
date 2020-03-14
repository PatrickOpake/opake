<?php

namespace OpakeAdmin\Service\ASCX12\HealthCareEligibility\E271;

class ValidationChecker
{
	protected $infoSource;
	protected $receiversSource;
	protected $subscribers;
	protected $dependents;

	protected $errors = [];

	public function __construct($response)
	{
		$this->info = $response->getInformationSources()[0];
		$this->receivers = $this->info->getInformationReceivers()[0];
		$this->subscribers = $this->receivers->getSubscribers()[0];
		$this->dependents = $this->subscribers->getDependents();
	}

	public function validate()
	{
		$this->errors = [];
		$this->checkInformationSourceLevel();
		$this->checkInformationSourceNameLevel();
		$this->checkInformationReceiverRequestValidation();
		$this->checkSubscriberRequestValidation();
		$this->checkSubscriberEligibilityRequestValidation();
		$this->checkDependentRequestValidation();
		$this->checkDependentEligibilityRequestValidation();

		return $this->errors;
	}

	protected function checkInformationSourceLevel()
	{
		$validationSegments = $this->info->getRequestValidation();
		if(!empty($validationSegments)) {
			$msg = $this->buildMsg($validationSegments);
			$this->addError($msg);
		}
	}

	protected function checkInformationSourceNameLevel()
	{
		$validationSegments = $this->info->getRequestValidation2();
		if(!empty($validationSegments)) {
			$msg = $this->buildMsg($validationSegments);
			$this->addError($msg);
		}
	}

	protected function checkInformationReceiverRequestValidation()
	{
		$validationSegments = $this->receivers->getRequestValidations();
		if(!empty($validationSegments)) {
			$msg = $this->buildMsg($validationSegments);
			$this->addError($msg);
		}
	}

	protected function checkSubscriberRequestValidation()
	{
		$validationSegments = $this->subscribers->getRequestValidations();
		if(!empty($validationSegments)) {
			$msg = $this->buildMsg($validationSegments);
			$this->addError($msg);
		}
	}

	protected function checkSubscriberEligibilityRequestValidation()
	{
		foreach ($this->subscribers->getEligibilities() as $eligibility) {
			$validationSegments = $eligibility->getRequestValidations();
			if(!empty($validationSegments)) {
				$msg = $this->buildMsg($validationSegments);
				$this->addError($msg);
			}
		}

	}

	protected function checkDependentRequestValidation()
	{
		if(!empty($this->dependents)) {
			$validationSegments = $this->dependents[0]->getRequestValidations();
			if(!empty($validationSegments)) {
				$msg = $this->buildMsg($validationSegments);
				$this->addError($msg);
			}
		}
	}

	protected function checkDependentEligibilityRequestValidation()
	{
		if(!empty($this->dependents)) {
			foreach ($this->dependents[0]->getEligibilities() as $eligibility) {
				$validationSegments = $eligibility->getRequestValidations();
				if (!empty($validationSegments)) {
					$msg = $this->buildMsg($validationSegments);
					$this->addError($msg);
				}
			}
		}
	}

	protected function addError($message)
	{
		$this->errors[] = $message;
	}

	protected function buildMsg($validationSegments)
	{
		$msg = '';
		foreach ($validationSegments as $segment) {
			if(!empty($segment->getRejectReasonCode())) {
				$msg .= $segment->getRejectReasonMsg();

			}
			if(!empty($segment->getFollowupActionCode())) {
				$msg .= ' - ' . $segment->getFollowUpActionMsg() . "\n";
			} else {
				$msg .= "\n";
			}
		}
		return $msg;
	}

}