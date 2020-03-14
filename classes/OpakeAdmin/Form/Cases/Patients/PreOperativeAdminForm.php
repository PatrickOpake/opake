<?php

namespace OpakeAdmin\Form\Cases\Patients;

use Opake\Form\AbstractForm;
use Opake\Helper\StringHelper;

class PreOperativeAdminForm extends AbstractForm
{
	const CHARACTER_LIMIT = 250;

	/**
	 * @param $value
	 * @return bool
	 */
	public function checkNamesLength($value)
	{
		if (is_array($value)) {
			foreach ($value as $array) {
				if (isset($array['name'])) {
					if (StringHelper::strlen($array['name']) > static::CHARACTER_LIMIT) {
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * @param $value
	 * @return bool
	 */
	public function checkConditionsLength($value)
	{
		if (is_array($value)) {
			foreach ($value as $array) {
				if (isset($array['reason'])) {
					if (StringHelper::strlen($array['reason']) > static::CHARACTER_LIMIT) {
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * @param \Opake\Extentions\Validate $validator
	 */
	protected function setValidationRules($validator)
	{
		$validator->field('height_ft')
			->rule('numeric', $this)
			->error('Height must be numeric');
		$validator->field('height_in')
			->rule('numeric', $this)
			->error('Height must be numeric');
		$validator->field('weight_lbs')
			->rule('numeric', $this)
			->error('Weight must be numeric');

		$validator->field('height_ft')->rule('max_length', 1)->error('Height Ft must contains only one digit');
		$validator->field('height_in')->rule('max_length', 2)->error('Height In must contains two digits');

		$validator->field('primary_care_phone')->rule('phone')->error('Incorrect phone format in Primary Care Physician Phone');
		$validator->field('transportation_phone')->rule('phone')->error('Incorrect phone format in Name of transportation Phone');
		$validator->field('caretaker_phone')->rule('phone')->error('Incorrect phone format in Name of Caretaker after procedure Phone');

		$validator->field('leave_message_phone')->rule('phone')->error('Incorrect phone format in Future Communication Phone');

		$validator->field('smoke_how_long_yrs')->rule('numeric', $this)->error('Smoke How Long Yrs must be numeric');
		$validator->field('smoke_packs_per_day')->rule('numeric', $this)->error('Smoke Packs Per Day must be numeric');
		$validator->field('drink_how_long_yrs')->rule('numeric', $this)->error('Drink How Long Yrs must be numeric');
		$validator->field('drink_drinks_per_week')->rule('numeric', $this)->error('Drinks Per Week must be numeric');
		$validator->field('illicit_drugs_how_long_yrs')->rule('numeric', $this)->error('Illicit Drugs How Long Yrs must be numeric');

		$validator->field('medications')->rule('callback', [$this, 'checkNamesLength'])
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Medications');
		$validator->field('allergies')->rule('callback', [$this, 'checkNamesLength'])
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Allergies');
		$validator->field('surgeries_hospitalizations')->rule('callback', [$this, 'checkNamesLength'])
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Surgeries or Hospitalizations');
		$validator->field('family_problems')->rule('callback', [$this, 'checkNamesLength'])
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Medical conditions or problems');
		$validator->field('family_anesthesia_problems')->rule('callback', [$this, 'checkNamesLength'])
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in history of anesthesia related problems');
		$validator->field('travel_outside')->rule('callback', [$this, 'checkNamesLength'])
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Travel Outside');
		$validator->field('communicable_diseases')->rule('callback', [$this, 'checkNamesLength'])
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Communicable Deseases');
		$validator->field('cultural_limitations')->rule('callback', [$this, 'checkNamesLength'])
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Cultural Limitations');

		$validator->field('steroids')->rule('max_length', static::CHARACTER_LIMIT)
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Steroids or Cortisone');
		$validator->field('allergic_to_latex_reason')->rule('max_length', static::CHARACTER_LIMIT)
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Allergic to Latex');
		$validator->field('allergic_to_food_reason')->rule('max_length', static::CHARACTER_LIMIT)
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Allergic to Food');
		$validator->field('allergic_other_reason')->rule('max_length', static::CHARACTER_LIMIT)
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Other Allergies');
		$validator->field('smoke_description')->rule('max_length', static::CHARACTER_LIMIT)
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Do you smoke');
		$validator->field('drink_description')->rule('max_length', static::CHARACTER_LIMIT)
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Do you drink');

		$validator->field('primary_care_name')
			->rule('max_length', static::CHARACTER_LIMIT)
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Primary Care Name');
		$validator->field('transportation_name')
			->rule('max_length', static::CHARACTER_LIMIT)
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Transportation Name');
		$validator->field('caretaker_name')
			->rule('max_length', static::CHARACTER_LIMIT)
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Caretaker Name');

		$validator->field('conditions')
			->rule('callback',  [$this, 'checkConditionsLength'])
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Conditions');

		$validator->field('illicit_drugs_description')
			->rule('max_length', static::CHARACTER_LIMIT)
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in Illicit Drugs Description');
		$validator->field('history_of_present_illness')
			->rule('max_length', static::CHARACTER_LIMIT)
			->error(static::CHARACTER_LIMIT . '  character limited exceeded in History of Present Illness');

	}

	protected function getFields()
	{
		return [
			'height_ft',
			'height_in',
			'weight_lbs',
			'medications',
			'steroids',
			'allergies',
			'allergic_to_latex',
			'allergic_to_latex_reason',
			'allergic_to_food',
			'allergic_to_food_reason',
			'allergic_other',
			'allergic_other_reason',
			'conditions',
			'surgeries_hospitalizations',
			'family_problems',
			'family_anesthesia_problems',
			'smoke',
			'smoke_how_long_yrs',
			'smoke_packs_per_day',
			'smoke_description',
			'drink',
			'drink_how_long_yrs',
			'drink_drinks_per_week',
			'drink_description',
			'travel_outside',
			'living_will',
			'primary_care_name',
			'primary_care_phone',
			'transportation_name',
			'transportation_phone',
			'caretaker_name',
			'caretaker_phone',
			'leave_message',
			'leave_message_phone',
			'confirmed_patient_demographics',
			'correction_made',
			'history_of_present_illness',
			'illicit_drugs',
			'illicit_drugs_how_long_yrs',
			'illicit_drugs_description',
			'communicable_diseases',
			'cultural_limitations',
			'pain_management'
		];
	}
}