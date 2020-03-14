<?php

namespace OpakePatients\Form\Cases;

use Opake\Form\AbstractForm;
use Opake\Model\AbstractModel;
use Opake\Model\Insurance\AbstractType;
use OpakePatients\Form\Cases\Insurances\AutoAccidentInsuranceForm;
use OpakePatients\Form\Cases\Insurances\DescriptionInsuranceForm;
use OpakePatients\Form\Cases\Insurances\RegularInsuranceForm;
use OpakePatients\Form\Cases\Insurances\WorkerCompInsuranceForm;

class InsuranceEditForm extends AbstractForm
{
	/**
	 * @var AbstractModel
	 */
	protected $registration;

	/**
	 * @param \Opake\Application $pixie
	 * @param AbstractModel $model
	 * @param AbstractModel $registration
	 */
	public function __construct($pixie, $model = null, $registration = null)
	{
		parent::__construct($pixie, $model);

		$this->registration = $registration;
	}

	/**
	 * @return AutoAccidentInsuranceForm|RegularInsuranceForm|WorkerCompInsuranceForm
	 */
	public function getDataModelForm()
	{
		$this->model->type = $this->getValueByName('type');
		$insuranceDataModel = $this->model->getInsuranceDataModel();

		if ($this->model->isAutoAccidentInsurance()) {
			return new AutoAccidentInsuranceForm($this->pixie, $insuranceDataModel, $this->registration, $this->model);
		}

		if ($this->model->isWorkersCompanyInsurance()) {
			return new WorkerCompInsuranceForm($this->pixie, $insuranceDataModel, $this->registration, $this->model);
		}

		if ($this->model->isDescriptionInsurance()) {
			return new DescriptionInsuranceForm($this->pixie, $insuranceDataModel, $this->registration, $this->model);
		}

		return new RegularInsuranceForm($this->pixie, $insuranceDataModel, $this->registration, $this->model);
	}

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('type')->rule('filled')->error('You must specify Insurance Type');
	}

	protected function getFields()
	{
		return [
			'order',
			'type',
		];
	}
}