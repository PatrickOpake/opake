<?php

namespace Opake\Model\Booking;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;
use Opake\Model\Cases\OperativeReport;
use \Opake\Model\Patient as StandardPatient;

class Patient extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'booking_patient';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'status' => StandardPatient::STATUS_ACTIVE,
		'dob' => null,
		'ssn' => null,
		'language_id' => null,
		'title' => null,
		'first_name' => '',
		'middle_name' => '',
		'parents_name' => '',
		'last_name' => '',
		'suffix' => null,
		'gender' => null,
		'race' => null,
		'ethnicity' => null,
		'status_marital' => null,
		'status_employment' => null,
		'employer' => '',
		'employer_phone' => null,
		'home_address' => null,
		'home_apt_number' => null,
		'home_state_id' => null,
		'custom_home_state' => null,
		'home_city_id' => null,
		'custom_home_city' => null,
		'home_zip_code' => null,
		'home_country_id' => null,
		'home_phone' => null,
		'home_email' => null,
		'ec_name' => null,
		'ec_relationship' => null,
		'ec_phone_number' => null,
		'additional_phone' => null,
		'point_of_contact_phone' => null,
		'point_of_contact_phone_type' => null,
		'photo_id' => null,
		//Out of network
		'oon_benefits' => null,
		'pre_certification_required' => null,
		'pre_certification_obtained' => null,
		'self_funded' => null,
		'co_pay' => null,
		'co_insurance' => null,
		'patients_responsibility' => null,
		'individual_deductible' => null,
		'individual_met_to_date' => null,
		'individual_remaining_1' => null,
		'individual_remaining_2' => null,
		'individual_out_of_pocket_maximum' => null,
		'family_deductible' => null,
		'family_met_to_date' => null,
		'family_remaining_1' => null,
		'family_remaining_2' => null,
		'family_out_of_pocket_maximum' => null,
		'yearly_maximum' => null,
		'lifetime_maximum' => null,
		'coverage_type' => null,
		'oon_reimbursement' => null,
		'effective_date' => '',
		'term_date' => '',
		'renewal_date' => '',
		//Questions
		'is_oon_benefits_cap' => null,
		'oon_benefits_cap' => null,
		'is_asc_benefits_cap' => null,
		'asc_benefits_cap' => null,
		'is_pre_existing_clauses' => null,
		'pre_existing_clauses' => null,
		'body_part' => null,
		'is_clauses_pertaining' => null,
		//Free Text
		'subscribers_name' => '',
		'authorization_number' => '',
		'expiration' => '',
		'spoke_with' => '',
		'reference_number' => '',
		'staff_member_name' => '',
		'date' => '',
		'insurance_verified' => null,
		'is_pre_authorization_completed' => null,
		'home_phone_type' => null,
		'additional_phone_type' => null,
		'ec_phone_type' => null,
		'relationship' => null,
	];

	protected $belongs_to = [
		'language' => [
			'model' => 'Language',
			'key' => 'language_id'
		],

		'home_country' => [
			'model' => 'Geo_Country',
			'key' => 'home_country_id'
		],
		'home_state' => [
			'model' => 'Geo_State',
			'key' => 'home_state_id'
		],
		'home_city' => [
			'model' => 'Geo_City',
			'key' => 'home_city_id'
		],
		'organization' => [
			'model' => 'Organization',
			'key' => 'organization_id'
		],
		'photo' => [
			'model' => 'UploadedFile_Image',
			'key' => 'photo_id',
			'cascade_delete' => true
		]
	];

	protected $has_many = [
		'insurances' => [
			'model' => 'Booking_PatientInsurance',
			'key' => 'booking_patient_id',
			'cascade_delete' => true
		]
	];

	public function getGender()
	{
		$genders = StandardPatient::getGenderList();
		return isset($genders[$this->gender]) ? $genders[$this->gender] : '';
	}

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('first_name')->rule('filled')->error('You must specify First Name');
		$validator->field('last_name')->rule('filled')->error('You must specify Last Name');

		$validator->field('ssn')->rule('filled')->error('You must specify Social Security #');
		$validator->field('ssn')->rule('callback', function ($value) {
			$patient = $this->pixie->orm->get('Patient')
				->where('ssn', $value)
				->where('id', '<>', $this->id());
			if (isset($this->organization_id)) {
				$patient->where('organization_id', $this->organization_id);
			}
			$model = $patient->find();
			return !$model->loaded();
		})->error(sprintf('Patient with SSN %s already exists', $this->ssn));
		$validator->field('ssn')->rule('numeric', $this)->error('The Social Security # field must be numeric');
		$validator->field('ssn')->rule('min_length', 9)->error('The Social Security # must be equal to 9 characters');
		$validator->field('ssn')->rule('max_length', 9)->error('The Social Security # must be equal to 9 characters');
		$validator->field('status_marital')->rule('filled')->error('You must specify Marital Status');
		$validator->field('gender')->rule('filled')->error('You must specify Gender');
		$validator->field('race')->rule('filled')->error('You must specify Race');
		$validator->field('ethnicity')->rule('filled')->error('You must specify Ethnicity');
		$validator->field('language_id')->rule('filled')->error('You must specify Preferred Language');
		$validator->field('dob')->rule('filled')->error('You must specify Date of Birth');
		$validator->field('dob')->rule('date')->error('Incorrect Date of Birth format');

		$validator->field('home_address')->rule('filled')->error('You must specify Home Address');
		if ($this->home_country_id == 235) {
			$validator->field('home_city_id')->rule('filled')->error('You must specify Home City');
			$validator->field('home_state_id')->rule('filled')->error('You must specify Home State');
			$validator->field('home_zip_code')->rule('filled')->error('You must specify Home Zip code');
		} else {
			$validator->field('custom_home_city')->rule('filled')->error('You must specify Home City');
		}
		$validator->field('home_country_id')->rule('filled')->error('You must specify Home Country');
		$validator->field('home_phone')->rule('filled')->error('You must specify Phone #');
		$validator->field('home_phone')->rule('phone')->error('Incorrect Phone #');
		$validator->field('home_email')->rule('email')->error('Invalid Email Address');

		$validator->field('ec_name')->rule('filled')->error('You must specify Emergency Contact Name');
		$validator->field('ec_relationship')->rule('filled')->error('You must specify Relationship to Patient');
		$validator->field('ec_phone_number')->rule('filled')->error('You must specify Emergency Phone #');
		$validator->field('ec_phone_number')->rule('phone')->error('Incorrect Emergency Phone # format');
		$validator->field('employer_phone')->rule('phone')->error('Incorrect Employer Phone # format');
		$validator->field('additional_phone')->rule('phone')->error('Incorrect Additional Phone # format');
		$validator->field('point_of_contact_phone')->rule('phone')->error('Incorrect Point of Contact Phone # format');


		return $validator;
	}

	/**
	 * @return string
	 */
	public function getFullName()
	{
		return $this->first_name . ' ' . $this->last_name;
	}

	/**
	 * @return string
	 */
	public function getFullNameForBooking()
	{
		return sprintf('%s, %s', $this->last_name, $this->first_name);
	}

	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return $this->home_email;
	}

	public function getAge()
	{
		$date = new \DateTime($this->dob);
		$now = new \DateTime();
		$interval = $now->diff($date);
		return $interval->y;
	}

	public function fromArray($data)
	{
		if (isset($data->language) && $data->language) {
			$data->language_id = $data->language->id;
		}

		if (isset($data->home_country) && isset($data->home_country->id)) {
			$data->home_country_id = $data->home_country->id;
		}
		if (isset($data->home_state) && (isset($data->home_state->id) || is_null($data->home_state->id))) {
			$data->home_state_id = $data->home_state->id;
		}
		if (isset($data->home_city) && isset($data->home_city->id)) {
			$data->home_city_id = $data->home_city->id;
		}
		if (isset($data->home_zip_code) && is_object($data->home_zip_code)) {
			$data->home_zip_code = $data->home_zip_code->code;
		}

		if (isset($data->dob) && $data->dob) {
			$data->dob = TimeFormat::formatToDB($data->dob);
		}

		if (property_exists($data, 'home_city')) {
			if (!empty($data->home_city->id)) {
				$data->home_city_id = $data->home_city->id;
			} else if (!empty($data->home_city->name)) {

				$organizationId = null;
				if (isset($data->organization->id)) {
					$organizationId = $data->organization->id;
				} else if (isset($data->organization_id)) {
					$organizationId = $data->organization_id;
				} else {
					throw new \Exception('Can\'t add new city without ID of organization');
				}

				$model = $this->pixie->orm->get('Geo_City');
				$city = $model->addCustomRecord($organizationId, $data->home_city->state_id, $data->home_city->name);
				$data->home_city_id = $city->id();
			} else if ($data->home_city === null) {
				$data->home_city_id = null;
			}
			unset($data->home_city);
		}

		return $data;
	}

	public function isSelf($user)
	{
		if (!$this->id()) {
			return true;
		}

		$q = $this->pixie->db->query('select')
			->table('patient')
			->fields($this->pixie->db->expr('DISTINCT patient.id'))
			->join('case_registration', ['patient.id', 'case_registration.patient_id'], 'inner')
			->join('case', ['case_registration.case_id', 'case.id'], 'inner')
			->join('case_user', ['case.id', 'case_user.case_id'], 'inner');


		//allowed for all professions
		$userPracticeGroupIds = $user->getPracticeGroupIds();

		if ($userPracticeGroupIds) {
			$q->join('user', ['user.id', 'case_user.user_id'], 'inner');
			$q->join('user_practice_groups', ['case_user.user_id', 'user_practice_groups.user_id'], 'left');
			$q->where('user.organization_id', $user->organization_id);
			$q->where('and', [
				['or', ['user_practice_groups.practice_group_id', 'IN', $this->pixie->db->expr("(" . implode(',', $userPracticeGroupIds) . ")")]],
				['or', ['case_user.user_id', $user->id()]]
			]);
		} else {
			$q->where('case_user.user_id', $user->id());
		}

		$q->where('patient.id', $this->id());

		return $q->execute()->valid();
	}

	public function getFullNameForLabel()
	{
		return substr($this->last_name . ', ' . $this->first_name, 0, 27);
	}

	public function getCoverageTypeName()
	{
		$names = StandardPatient::getCoverageTypes();
		if ($this->coverage_type && isset($names[$this->coverage_type])) {
			return $names[$this->coverage_type];
		}

		return '';
	}


	public function toArray()
	{
		$data = parent::toArray();

		$insurances = [];
		foreach ($this->insurances->find_all() as $item) {
			$insurances[] = $item->toArray();
		}
		$data['insurances'] = $insurances;
		$data['display_point_of_contact'] = (bool)$this->organization->sms_template->poc_sms;
		$data['dob'] = $this->dob;
		$data['photo'] = $this->getPhoto('tiny');
		$data['photo_default'] = $this->getPhoto('default');
		$data['home_country_name'] = ($this->home_country->loaded()) ? $this->home_country->name : '';
		$data['home_city_name'] = (($this->custom_home_city) ? : (($this->home_city->loaded()) ? $this->home_city->name : ''));
		$data['home_state_name'] = (($this->custom_home_state) ? : (($this->home_state->loaded()) ? $this->home_state->name : ''));

		$data['home_country'] = ($this->home_country && $this->home_country->loaded()) ? $this->home_country->toArray() : null;
		$data['home_state'] = ($this->home_state && $this->home_state->loaded()) ? $this->home_state->toArray() : null;
		$data['home_city'] = ($this->home_city && $this->home_city->loaded()) ? $this->home_city->toArray() : null;

		$data = array_merge($data, [
			'is_oon_benefits_cap' => (int)$this->is_oon_benefits_cap,
			'is_asc_benefits_cap' => (int)$this->is_asc_benefits_cap,
			'is_pre_existing_clauses' => (int)$this->is_pre_existing_clauses,
			'is_clauses_pertaining' => (int)$this->is_clauses_pertaining,
			'oon_benefits' => (bool)$this->oon_benefits,
			'pre_certification_required' => (bool)$this->pre_certification_required,
			'pre_certification_obtained' => (bool)$this->pre_certification_obtained,
			'self_funded' => (bool)$this->self_funded,
			'insurance_verified' => (bool)$this->insurance_verified,
			'is_pre_authorization_completed' => (bool) $this->is_pre_authorization_completed,
		    'is_booking_patient' => true
		]);

		return $data;
	}

	public function toShortArray()
	{
		return [
			'id' => $this->id,
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'fullname' => $this->getFullName(),
			'ssn' => $this->ssn,
			'dob' => $this->dob,
			'home_phone' => $this->home_phone,
			'age' => $this->getAge(),
			'sex' => $this->getGender(),
			'status' => (int)$this->status
		];
	}

	public function getPhoto($size = null)
	{
		if ($photo = $this->getPhotoModel()) {
			return $photo->getThumbnailWebPath($size);
		}

		if ($size) {
			return '/i/user_profile_' . $size . '.png';
		}

		return '/i/user_profile.png';
	}

	/**
	 * @return \Opake\Model\UploadedFile\Image
	 */
	protected function getPhotoModel()
	{
		if ($this->photo_id) {
			if ($this->photo->loaded()) {
				return $this->photo;
			}
			if (!$this->photo->loaded() && $this->photo_id) {
				$model = $this->pixie->orm->get('UploadedFile_Image', $this->photo_id);
				if ($model->loaded()) {
					return $model;
				}
			}
		}

		return null;
	}

}
