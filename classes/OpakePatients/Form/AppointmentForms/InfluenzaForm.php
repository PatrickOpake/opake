<?php

namespace OpakePatients\Form\AppointmentForms;

use Opake\Form\AbstractForm;
use Opake\Helper\StringHelper;

class InfluenzaForm extends AbstractForm
{

	const CHARACTER_LIMIT = 250;

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('illnesses')->rule('callback', function($value) {

			if (isset($value['cough']) && isset($value['cough']['color'])) {
				if (StringHelper::strlen($value['cough']['color']) > static::CHARACTER_LIMIT) {
					return false;
				}
			}

			return true;

		})->error(static::CHARACTER_LIMIT . ' character limit exceeded in Cough Color');
	}

	protected function getFields()
	{
		return [
			'travel_outside',
			'flu_vaccine',
			'flu_vaccine_month',
			'travel_outside_date',
			'illnesses',
		];
	}
}