<?php

namespace OpakePatients\Model\Appointment\AlertType;

use Opake\Model\AbstractModel;

abstract class AbstractType
{

	const TYPE_FORMS = 'forms';
	const TYPE_INSURANCES = 'insurances';
	const TYPE_PATIENT_INFO = 'patient_info';

	/**
	 * @var AbstractModel
	 */
	protected $registration;

	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	public function __construct($pixie, $registration)
	{
		$this->registration = $registration;
		$this->pixie = $pixie;
	}

	/**
	 * @return bool
	 */
	public function isNeedToShowAlert()
	{
		$registration = $this->registration;

		$confirmKey = $this->getConfirmKey();
		$hiddenKey = $this->getHiddenKey();

		$hiddenModel = $registration->alert_hidden;
		$confirmModel = $registration->patient_confirm;

		if ($hiddenModel && $hiddenModel->loaded()) {
			if ($hiddenModel->{$hiddenKey}) {
				return false;
			}
		}

		if ($confirmModel && $confirmModel->loaded()) {
			if ($confirmModel->{$confirmKey}) {
				return false;
			}
		}

		return true;
	}

	public function hideAlert()
	{
		$registration = $this->registration;
		$hiddenModel = $registration->alert_hidden;
		$hiddenKey = $this->getHiddenKey();
		if (!($hiddenModel && $hiddenModel->loaded())) {
			$hiddenModel = $this->pixie->orm->get('Patient_Appointment_AlertHidden');
			$hiddenModel->patient_user_id = $registration->patient_id;
			$hiddenModel->case_registration_id = $registration->id();
		}

		$hiddenModel->{$hiddenKey} = 1;
		$hiddenModel->save();
	}

	public function getAlertData()
	{
		return [
			'appointment_id' => $this->registration->id(),
			'type' => $this->getType(),
			'link' => $this->getLink(),
			'label' => $this->getLabel()
		];
	}

	abstract protected function getHiddenKey();

	abstract protected function getConfirmKey();

	abstract protected function getType();

	abstract protected function getLink();

	abstract protected function getLabel();

	public static function getObjectByType($type, $pixie, $registration)
	{
		switch ($type) {
			case AbstractType::TYPE_PATIENT_INFO:
				return new \OpakePatients\Model\Appointment\AlertType\PatientInfo($pixie, $registration);
			case AbstractType::TYPE_INSURANCES:
				return new \OpakePatients\Model\Appointment\AlertType\Insurances($pixie, $registration);
			case AbstractType::TYPE_FORMS:
				return new \OpakePatients\Model\Appointment\AlertType\Forms($pixie, $registration);
		}

		throw new \Exception('Unknown alert type');
	}
}