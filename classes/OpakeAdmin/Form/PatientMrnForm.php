<?php

namespace OpakeAdmin\Form;

use Opake\Form\AbstractForm;

class PatientMrnForm extends AbstractForm
{
	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{

		$validator->field('mrn')->rule('filled')->error('You must specify MRN');

		$validator->field('mrn_year')->rule('callback', function ($value) {
			if (!$value) {
				return true;
			}

			return (bool)preg_match('/^[0-9]{1,2}$/', $value);
		})->error('MRN year must contain only two digits');

		$validator->field('mrn')->rule('callback', function ($value) {
			return (bool)preg_match('#^[0-9]*$#', $value);
		})->error('MRN must contain only numeric symbols');

		$validator->field('mrn')->rule('callback', function ($value) {
			$model = $this->getModel();
			$patient = $this->pixie->orm->get('Patient')
				->where('mrn', (int) $value)
				->where('mrn_year', (int) $this->getValueByName('mrn_year'));
			if ($model->loaded()) {
				$patient->where('id', '<>', $model->id());
			}
			if (isset($model->organization_id)) {
				$patient->where('organization_id', $model->organization_id);
			}
			$patient = $patient->find();
			return !$patient->loaded();
		})->error('MRN already exists!');
		$validator->field('mrn')
			->rule('max_length', 5)->error('Maximum MRN length is 5 digits');
	}

	protected function getFields()
	{
		return [
			'mrn',
			'mrn_year'
		];
	}
}