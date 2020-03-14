<?php

namespace OpakeAdmin\Form\Insurance;

use Opake\Form\AbstractForm;
use Opake\Model\AbstractModel;
use OpakeAdmin\Form\Insurance\InsuranceTypes\DescriptionInsuranceForm;
use OpakeAdmin\Form\Insurance\InsuranceTypes\RegularInsuranceForm;
use OpakeAdmin\Form\Insurance\InsuranceTypes\WorkerCompInsuranceForm;
use OpakeAdmin\Form\Insurance\InsuranceTypes\AutoAccidentInsuranceForm;

class InsuranceEditForm extends AbstractForm
{
	/**
	 * @var AbstractModel
	 */
	protected $registration;

	/**
	 * @param \Opake\Application $pixie
	 * @param AbstractModel $model
	 */
	public function __construct($pixie, $model = null)
	{
		parent::__construct($pixie, $model);
	}

	/**
	 * @return AutoAccidentInsuranceForm|RegularInsuranceForm|WorkerCompInsuranceForm
	 */
	public function getDataModelForm()
	{
		$this->model->type = $this->getValueByName('type');
		$insuranceDataModel = $this->model->getInsuranceDataModel();

		if ($this->model->isAutoAccidentInsurance()) {
			return new AutoAccidentInsuranceForm($this->pixie, $insuranceDataModel, $this->model);
		}

		if ($this->model->isWorkersCompanyInsurance()) {
			return new WorkerCompInsuranceForm($this->pixie, $insuranceDataModel, $this->model);
		}

		if ($this->model->isDescriptionInsurance()) {
			return new DescriptionInsuranceForm($this->pixie, $insuranceDataModel, $this->model);
		}

		return new RegularInsuranceForm($this->pixie, $insuranceDataModel, $this->model);
	}

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('type')->rule('filled_callback', function($value) {
			return (!empty($value));
		})->error('You must specify Insurance Type');
	}

	protected function getFields()
	{
		return [
			'order',
			'type',
		    'selected_insurance_id'
		];
	}
}