<?php

namespace Opake\Model;

use Opake\Helper\TimeFormat;
use Opake\Model\Cases\OperativeReport;
use Opake\Model\Insurance\AbstractType;

class Patient extends AbstractModel
{

	const TITLE_MR = 1;
	const TITLE_MS = 2;
	const TITLE_MISS = 3;
	const TITLE_MRS = 4;
	const TITLE_DR = 5;
	const TITLE_HON = 6;
	const TITLE_REV = 7;
	const TITLE_PVT = 8;
	const TITLE_CPL = 9;
	const TITLE_SGT = 10;
	const TITLE_MAJ = 11;
	const TITLE_CAPT = 12;
	const TITLE_CMDR = 13;
	const TITLE_LT = 14;
	const TITLE_LT_COL = 15;
	const TITLE_COL = 16;
	const TITLE_GEN = 17;

	const SUFFIX_I = 1;
	const SUFFIX_II = 2;
	const SUFFIX_III = 3;
	const SUFFIX_IV = 4;
	const SUFFIX_JR = 5;
	const SUFFIX_SR = 6;
	const SUFFIX_MD = 7;
	const SUFFIX_ESQ = 8;

	const GENDER_MALE = 1;
	const GENDER_FEMALE = 2;
	const GENDER_TRANSGENDER = 3;
	const GENDER_UNKNOWN = 4;

	const RACE_AMERICAN_INDIAN_ALASKA_NATIVE = 1;
	const RACE_ASIAN = 2;
	const RACE_AFRICAN_AMERICAN = 3;
	const RACE_NATIVE_PACIFIC = 4;
	const RACE_WHITE = 5;
	const RACE_DECLINED_COMMENT = 6;

	const ETHNICITY_HISPANIC_LATINO = 1;
	const ETHNICITY_NOT_HISPANIC_LATINO = 2;
	const ETHNICITY_DECLINED_COMMENT = 3;

	const STATUS_MARITAL_SINGLE = 1;
	const STATUS_MARITAL_MARRIED = 2;
	const STATUS_MARITAL_WIDOWED = 3;
	const STATUS_MARITAL_DIVORCED = 4;
	const STATUS_MARITAL_OTHER = 5;

	const STATUS_EMPLOYMENT_EMPLOYED = 1;
	const STATUS_EMPLOYMENT_FT_STUDENT = 2;
	const STATUS_EMPLOYMENT_PT_STUDENT = 3;
	const STATUS_EMPLOYMENT_RETIRED = 4;
	const STATUS_EMPLOYMENT_UNEMPLOYED = 5;

	const PHONE_HOME = 1;
	const PHONE_WORK = 2;
	const PHONE_CELL = 3;
	const PHONE_OTHER = 4;

	const STATUS_ACTIVE = 1;
	const STATUS_ARCHIVE = 0;

	public $id_field = 'id';
	public $table = 'patient';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'status' => self::STATUS_ACTIVE,
		'mrn' => null,
		'mrn_year' => null,
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
		'point_of_contact_phone_type' => self::PHONE_CELL,
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

	protected $has_one = [
		'portal_user' => [
			'model' => 'Patient_User',
			'key' => 'patient_id'
		],
	];

	protected $has_many = [
		'insurances' => [
			'model' => 'Patient_Insurance',
			'key' => 'patient_id',
			'cascade_delete' => true
		],
		'charts' => [
			'model' => 'Patient_Chart',
			'key' => 'patient_id',
			'cascade_delete' => true
		],
		'financial_documents' => [
			'model' => 'Patient_FinancialDocument',
			'key' => 'patient_id',
			'cascade_delete' => true
		],
	];

	/**
	 * @var array
	 */
	protected $baseFormatter = [
		'class' => '\Opake\Formatter\ModelMethodFormatter',
		'includeBelongsTo' => true
	];

	protected $formatters = [
		'BillingLedgerListEntry' => [
			'class' => '\Opake\Formatter\Billing\Ledger\Patient\ListEntryFormatter',
		],
	    'BillingLedger' => [
		    'class' => '\Opake\Formatter\Billing\Ledger\Patient\PatientFormatter'
	    ],
		'BillingPatientStatementListEntry' => [
			'class' => '\Opake\Formatter\Billing\PatientStatement\ListEntryFormatter',
		],
		'BillingItemizedBillListEntry' => [
			'class' => '\Opake\Formatter\Billing\ItemizedBill\ListEntryFormatter',
		],
	];

	public function initMrn($organizationId)
	{
		$nextNumber = $this->pixie->orm->get('Patient_MrnCounter')
			->incrementCounterForOrganization($organizationId);

		$date = new \DateTime();
		$year = (int) $date->format('y');

		$this->mrn = $nextNumber;
		$this->mrn_year = $year;
	}

	/**
	 * @return string
	 */
	public function getGender()
	{
		$genders = self::getGenderList();
		return (isset($genders[$this->gender]) ? $genders[$this->gender] : '');
	}

	/**
	 * @return string
	 */
	public function getSexLetter()
	{
		if ($this->gender == self::GENDER_MALE) {
			return 'M';
		} else if ($this->gender == self::GENDER_FEMALE) {
			return 'F';
		}

		return '';
	}

	/**
	 * @return string
	 */
	public function getGenderTitle()
	{
		if ($this->gender == \Opake\Model\Patient::GENDER_MALE) {
			return 'He';
		}

		if ($this->gender == \Opake\Model\Patient::GENDER_FEMALE) {
			return 'She';
		}

		if ($this->gender == \Opake\Model\Patient::GENDER_TRANSGENDER || $this->gender == \Opake\Model\Patient::GENDER_UNKNOWN) {
			return 'They';
		}

		return '';
	}

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('first_name')->rule('filled')->error('You must specify First Name');
		$validator->field('last_name')->rule('filled')->error('You must specify Last Name');

		$validator->field('mrn')->rule('filled')->error('You must specify MRN');

		$validator->field('mrn')->rule('callback', function ($value) {
			$patient = $this->pixie->orm->get('Patient')
				->where('mrn', (int) $value)
				->where('mrn_year', (int) $this->mrn_year);
			if ($this->loaded()) {
				$patient->where('id', '<>', $this->id());
			}
			if (isset($this->organization_id)) {
				$patient->where('organization_id', $this->organization_id);
			}
			$model = $patient->find();
			return !$model->loaded();
		})->error('MRN already exists!');

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


	public function getNameAndDobValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('first_name')->rule('filled')->error('You must specify First Name');
		$validator->field('last_name')->rule('filled')->error('You must specify Last Name');
		$validator->field('dob')->rule('filled')->error('You must specify Date of Birth');
		$validator->field('dob')->rule('date')->error('Incorrect Date of Birth format');

		return $validator;
	}

	/**
	 * @return string
	 */
	public function getFullName()
	{
		return $this->first_name . ' ' . $this->last_name;
	}

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

	public function getDocuments()
	{
		$model = $this->pixie->orm->get('Cases_Registration_Document')
			->with('file', 'case_registration');

		$model->query
			->join(['case_registration', 'cr'], [$model->table . '.case_registration_id', 'cr.id'], 'inner')
			->where('cr.patient_id', $this->id)
			->where($model->table . '.uploaded_file_id', '<>', 'NULL')
			->order_by($model->table . '.uploaded_date', 'desc');
		return $model->find_all()->as_array();
	}

	public function getOperativeReports()
	{
		$model = $this->pixie->orm->get('Cases_OperativeReport');
		$search = new \OpakeAdmin\Model\Search\Cases\OperativeReport($this->pixie);
		$results = $search->searchForPatient($model, $this->id);
		return $results;
	}

	public function getSelectedInsurances()
	{
		return $this->insurances
			->where('order', 'IS NOT NULL', $this->pixie->db->expr(''))
			->order_by('order', 'ASC')
			->find_all();
	}

	public function getAllInsurances()
	{
		return $this->insurances
			->order_by($this->pixie->db->expr('ISNULL(`order`)'), 'ASC')
			->order_by('order', 'ASC')
			->find_all();
	}

	public function getPrimaryInsurance()
	{
		$insurance = $this->insurances
			->where('order', 'IS NOT NULL', $this->pixie->db->expr(''))
			->order_by('order', 'ASC')
			->find();

		return ($insurance->loaded()) ? $insurance : null;
	}

	public function fromRegistration(\Opake\Model\Cases\Registration $reg)
	{
		$this->organization_id = $reg->organization_id;
		$this->title = $reg->title;
		$this->suffix = $reg->suffix;
		$this->first_name = $reg->first_name;
		$this->middle_name = $reg->middle_name;
		$this->last_name = $reg->last_name;
		$this->ssn = $reg->ssn;
		$this->gender = $reg->gender;
		$this->race = $reg->race;
		$this->dob = $reg->dob;
		$this->ethnicity = $reg->ethnicity;
		$this->language_id = $reg->language_id;
		$this->status_marital = $reg->status_marital;
		$this->status_employment = $reg->status_employment;
		$this->employer = $reg->employer;
		$this->employer_phone = $reg->employer_phone;
		$this->home_address = $reg->home_address;
		$this->home_apt_number = $reg->home_apt_number;
		$this->home_state_id = $reg->home_state_id;
		$this->custom_home_state = $reg->custom_home_state;
		$this->home_city_id = $reg->home_city_id;
		$this->custom_home_city = $reg->custom_home_city;
		$this->home_zip_code = $reg->home_zip_code;
		$this->home_country_id = $reg->home_country_id;
		$this->home_phone = $reg->home_phone;
		$this->home_phone_type = $reg->home_phone_type;
		$this->home_email = $reg->home_email;
		$this->ec_name = $reg->ec_name;
		$this->ec_relationship = $reg->ec_relationship;
		$this->ec_phone_number = $reg->ec_phone_number;
		$this->additional_phone = $reg->additional_phone;
		$this->additional_phone_type = $reg->additional_phone_type;
		$this->point_of_contact_phone = $reg->point_of_contact_phone;
		$this->point_of_contact_phone_type = $reg->point_of_contact_phone_type;
		$this->ec_phone_type = $reg->ec_phone_type;
		$this->parents_name = $reg->parents_name;
		$this->relationship = $reg->relationship;

		$this->oon_benefits = $reg->oon_benefits;
		$this->pre_certification_required = $reg->pre_certification_required;
		$this->pre_certification_obtained = $reg->pre_certification_obtained;
		$this->self_funded = $reg->self_funded;
		$this->co_pay = $reg->co_pay;
		$this->co_insurance = $reg->co_insurance;
		$this->patients_responsibility = $reg->patients_responsibility;
		$this->individual_deductible = $reg->individual_deductible;
		$this->individual_met_to_date = $reg->individual_met_to_date;
		$this->family_deductible = $reg->family_deductible;
		$this->family_met_to_date = $reg->family_met_to_date;
		$this->family_remaining_1 = $reg->family_remaining_1;
		$this->family_remaining_2 = $reg->family_remaining_2;
		$this->family_out_of_pocket_maximum = $reg->family_out_of_pocket_maximum;
		$this->yearly_maximum = $reg->yearly_maximum;
		$this->lifetime_maximum = $reg->lifetime_maximum;
		$this->coverage_type = $reg->coverage_type;
		$this->effective_date = $reg->effective_date;
		$this->term_date = $reg->term_date;
		$this->renewal_date = $reg->renewal_date;
		$this->individual_out_of_pocket_maximum = $reg->individual_out_of_pocket_maximum;
		$this->individual_remaining_1 = $reg->individual_remaining_1;
		$this->individual_remaining_2 = $reg->individual_remaining_2;
		$this->is_oon_benefits_cap = $reg->is_oon_benefits_cap;
		$this->oon_benefits_cap = $reg->oon_benefits_cap;
		$this->is_asc_benefits_cap = $reg->is_asc_benefits_cap;
		$this->asc_benefits_cap = $reg->asc_benefits_cap;
		$this->is_pre_existing_clauses = $reg->is_pre_existing_clauses;
		$this->pre_existing_clauses = $reg->pre_existing_clauses;
		$this->body_part = $reg->body_part;
		$this->is_clauses_pertaining = $reg->is_clauses_pertaining;
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

		if (isset($data->mrn)) {
			$data->mrn = (int) $data->mrn;
		}

		if (isset($data->mrn_year)) {
			$data->mrn_year = (int) $data->mrn_year;
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

	/**
	 * @return string
	 */
	public function getFormattedMrn()
	{
		if ($this->mrn !== null) {
			return str_pad((string) $this->mrn, 5, '0', STR_PAD_LEFT);
		}

		return '';
	}

	/**
	 * @return string
	 */
	public function getFormattedMrnYear()
	{
		if ($this->mrn_year !== null) {
			return str_pad((string) $this->mrn_year, 2, '0', STR_PAD_LEFT);
		}

		return '';
	}

	/**
	 * @return string
	 */
	public function getFullMrn()
	{
		$mrn = $this->getFormattedMrn();
		if ($mrnYear = $this->getFormattedMrnYear()) {
			$mrn .= '-' . $mrnYear;
		}

		return $mrn;
	}

	public function getCoverageTypeName()
	{
		$names = \Opake\Model\Patient::getCoverageTypes();
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
		$data['is_patient_portal_enabled'] = $this->isPatientPortalEnabled();
		$data['can_register_on_portal'] = $this->canRegisterOnPortal();
		$data['is_registered_on_portal'] = $this->isPatientRegisteredOnPortal();
		$data['photo'] = $this->getPhoto('tiny');
		$data['photo_default'] = $this->getPhoto('default');
		$data['date_created'] = TimeFormat::getDateTime($this->portal_user->created);
		$data['show_insurance_banner'] = (bool) $this->portal_user->show_insurance_banner;
		$data['home_country_name'] = ($this->home_country->loaded()) ? $this->home_country->name : '';
		$data['home_city_name'] = (($this->custom_home_city) ? : (($this->home_city->loaded()) ? $this->home_city->name : ''));
		$data['home_state_name'] = (($this->custom_home_state) ? : (($this->home_state->loaded()) ? $this->home_state->name : ''));

		$data['home_country'] = ($this->home_country && $this->home_country->loaded()) ? $this->home_country->toArray() : null;
		$data['home_state'] = ($this->home_state && $this->home_state->loaded()) ? $this->home_state->toArray() : null;
		$data['home_city'] = ($this->home_city && $this->home_city->loaded()) ? $this->home_city->toArray() : null;

		$data['mrn'] = $this->getFormattedMrn();
		$data['mrn_year'] = $this->getFormattedMrnYear();
		$data['full_mrn'] = $this->getFullMrn();
		$data['has_flagged_comments'] = $this->hasFlaggedComments();
		$data['has_billing_flagged_comments'] = $this->hasBillingFlaggedComments();

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
			'is_pre_authorization_completed' => (bool) $this->is_pre_authorization_completed
		]);

		return $data;
	}

	public function toShortArray()
	{
		$op_reports = $this->getOperativeReports();
		return [
			'id' => $this->id,
			'mrn' => $this->getFormattedMrn(),
			'mrn_year' => $this->getFormattedMrnYear(),
			'full_mrn' => $this->getFullMrn(),
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'fullname' => $this->getFullName(),
			'ssn' => $this->ssn,
			'dob' => $this->dob,
			'home_phone' => $this->home_phone,
			'age' => $this->getAge(),
			'sex' => $this->getGender(),
			'count_cases' => count($op_reports),
			'status' => (int)$this->status
		];
	}

	public function toBookingExistingArray()
	{
		return [
			'id' => $this->id,
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'dob' => $this->dob,
			'home_phone' => $this->home_phone
		];
	}

	public function canRegisterOnPortal()
	{
		if ($this->first_name && $this->last_name && $this->home_email) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function isPatientRegisteredOnPortal()
	{
		return ($this->portal_user && $this->portal_user->loaded());
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

	public function isPatientPortalEnabled()
	{
		if ($this->organization && $this->organization->loaded()) {
			$organizationPermissions = new \Opake\Permissions\Organization\OrganizationLevel($this->organization);
			$permissions = $organizationPermissions->getOrganizationPermissions();

			if (!empty($permissions['patient_portal.login'])) {
				$portal = $this->organization->portal;
				if ($portal && $portal->loaded()) {
					if ($portal->active) {
						return true;
					}
				}
			}
		}

		return false;
	}

	protected function addValidatorMrnRules($validator)
	{


	}

	public function fromBookingPatient(\Opake\Model\Booking\Patient $bookingPatient)
	{
		$this->organization_id = $bookingPatient->organization_id;
		$this->title = $bookingPatient->title;
		$this->suffix = $bookingPatient->suffix;
		$this->first_name = $bookingPatient->first_name;
		$this->middle_name = $bookingPatient->middle_name;
		$this->last_name = $bookingPatient->last_name;
		$this->parents_name = $bookingPatient->parents_name;
		$this->ssn = $bookingPatient->ssn;
		$this->gender = $bookingPatient->gender;
		$this->race = $bookingPatient->race;
		$this->dob = $bookingPatient->dob;
		$this->ethnicity = $bookingPatient->ethnicity;
		$this->language_id = $bookingPatient->language_id;
		$this->status_marital = $bookingPatient->status_marital;
		$this->status_employment = $bookingPatient->status_employment;
		$this->employer = $bookingPatient->employer;
		$this->employer_phone = $bookingPatient->employer_phone;
		$this->additional_phone = $bookingPatient->additional_phone;
		$this->additional_phone_type = $bookingPatient->additional_phone_type;
		$this->point_of_contact_phone = $bookingPatient->point_of_contact_phone;
		$this->point_of_contact_phone_type = $bookingPatient->point_of_contact_phone_type;
		$this->home_address = $bookingPatient->home_address;
		$this->home_apt_number = $bookingPatient->home_apt_number;
		$this->home_state_id = $bookingPatient->home_state_id;
		$this->custom_home_state = $bookingPatient->custom_home_state;
		$this->home_city_id = $bookingPatient->home_city_id;
		$this->custom_home_city = $bookingPatient->custom_home_city;
		$this->home_zip_code = $bookingPatient->home_zip_code;
		$this->home_country_id = $bookingPatient->home_country_id;
		$this->home_phone = $bookingPatient->home_phone;
		$this->home_phone_type = $bookingPatient->home_phone_type;
		$this->home_email = $bookingPatient->home_email;
		$this->ec_name = $bookingPatient->ec_name;
		$this->ec_relationship = $bookingPatient->ec_relationship;
		$this->ec_phone_number = $bookingPatient->ec_phone_number;
		$this->ec_phone_type = $bookingPatient->ec_phone_type;
		$this->relationship = $bookingPatient->relationship;
	}

	protected function hasFlaggedComments()
	{
		$caseNotes = $this->pixie->orm->get('Cases_Note')->where('patient_id', $this->id);

		if ($caseNotes->count_all()) {
			return true;
		}

		return false;
	}

	protected function hasBillingFlaggedComments()
	{
		$caseNotes = $this->pixie->orm->get('Billing_Note')->where('patient_id', $this->id);

		if ($caseNotes->count_all()) {
			return true;
		}

		return false;
	}

	public static function getTitlesList()
	{
		return [
			1 => 'Mr.',
			2 => 'Ms',
			3 => 'Miss',
			4 => 'Mrs.',
			5 => 'Dr.',
			6 => 'Hon',
			7 => 'Rev',
			8 => 'Pvt',
			9 => 'Cpl',
			10 => 'Sgt',
			11 => 'Maj',
			12 => 'Capt',
			13 => 'Cmdr',
			14 => 'Lt',
			15 => 'Lt Col',
			16 => 'Col',
			17 => 'Gen'
		];
	}

	public static function getSuffixesList()
	{
		return [
			1 => 'I',
			2 => 'II',
			3 => 'III',
			4 => 'IV',
			5 => 'Jr.',
			6 => 'Sr.',
			7 => 'M.D.',
			8 => 'Esq.'
		];
	}

	public static function getGendersList()
	{
		return [
			1 => 'Male',
			2 => 'Female',
			3 => 'Transgender',
			4 => 'Unknown'
		];
	}

	public static function getRacesList()
	{
		return [
			1 => 'American Indian or Alaskan Native',
			2 => 'Asian',
			3 => 'African American',
			4 => 'Native Hawaiian or Other Pacific Islander',
			5 => 'White',
			6 => 'Patient Declined to Comment'
		];
	}

	public static function getEthnicityList()
	{
		return [
			1 => 'Hispanic or Latino',
			2 => 'Not Hispanic or Latino',
			3 => 'Patient Declined to Comment'
		];
	}

	public static function getMartialStatusesList()
	{
		return [
			1 => 'Single',
			2 => 'Married',
			3 => 'Widowed',
			4 => 'Divorced',
			5 => 'Other'
		];
	}

	public static function getEmploymentStatusesList()
	{
		return [
			1 => 'Employed',
			2 => 'Full-Time Student',
			3 => 'Part-Time Student',
			4 => 'Retired',
			5 => 'Unemployed'
		];
	}

	public static function getInsuranceTypesList()
	{
		return AbstractType::getInsuranceTypesList();
	}

	public static function getInsurancePrimaryList()
	{
		return [
			1 => 'Yes',
			2 => 'No'
		];
	}

	public static function getRelationshipToInsuredList()
	{
		return [
			0 => 'Self',
			1 => 'Husband',
			2 => 'Wife',
			3 => 'Parent',
			4 => 'Sibling',
			5 => 'Child',
			6 => 'Other',
		    7 => 'Spouse',
		    8 => 'Employee',
		    9 => 'Unknown',
		    10 => 'Organ Donor',
		    11 => 'Cadaver Donor',
		    12 => 'Life Partner',
		    13 => 'Other Relationship'
		];
	}

	public static function getInsuranceTitlesList()
	{
		return [
			0 => 'Primary Insurance',
			1 => 'Secondary Insurance',
			2 => 'Tertiary Insurance',
			3 => 'Quaternary Insurance',
			4 => 'Other Insurance',
			5 => 'Other Insurance',
			6 => 'Other Insurance',
			7 => 'Other Insurance',
			8 => 'Other Insurance',
			9 => 'Other Insurance'
		];
	}

	public static function getCoverageTypes()
	{
		return [
			0 => '',
			1 => 'Single',
			2 => 'Family',
			3 => 'Parent & Child',
			4 => 'Husband & Wife'
		];
	}

	public static function getPhoneTypes()
	{
		return [
			self::PHONE_HOME => 'Home',
			self::PHONE_WORK => 'Work',
			self::PHONE_CELL => 'Cell',
			self::PHONE_OTHER => 'Other'
		];
	}

	public static function getRelationshipList()
	{
		return [
			1 => 'Spouse',
			2 => 'Relative',
			3 => 'Friend',
			4 => 'Other'
		];
	}

	public static function getGenderList()
	{
		return [
			self::GENDER_MALE => 'Male',
			self::GENDER_FEMALE => 'Female',
			self::GENDER_TRANSGENDER => 'Transgender',
			self::GENDER_UNKNOWN => 'Unknown'
		];
	}
}
