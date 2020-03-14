<?php

namespace Opake\Model\Cases;

use Opake\Helper\TimeFormat;
use Opake\Model\AbstractModel;
use Opake\Model\Cases\Registration\Insurance\Verification;

/**
 * A registration info related to the case
 *
 * @property-read \Opake\Model\Patient $patient
 * @package Opake\Model\Cases
 */
class Registration extends AbstractModel
{

	const RELATIONSHIP_TO_INSURED_SELF = 0;
	const RELATIONSHIP_TO_INSURED_HUSBAND = 1;
	const RELATIONSHIP_TO_INSURED_WIFE = 2;
	const RELATIONSHIP_TO_INSURED_PARENT = 3;
	const RELATIONSHIP_TO_INSURED_SIBLING = 4;
	const RELATIONSHIP_TO_INSURED_CHILD = 5;
	const RELATIONSHIP_TO_INSURED_OTHER = 6;
	const RELATIONSHIP_TO_INSURED_SPOUSE = 7;
	const RELATIONSHIP_TO_INSURED_EMPLOYEE = 8;
	const RELATIONSHIP_TO_INSURED_UNKNOWN = 9;
	const RELATIONSHIP_TO_INSURED_ORGAN_DONOR = 10;
	const RELATIONSHIP_TO_INSURED_CADAVER_DONOR = 11;
	const RELATIONSHIP_TO_INSURED_LIFE_PARTNER = 12;
	const RELATIONSHIP_TO_INSURED_OTHER_RELATIONSHIP = 13;

	const STATUS_BEGIN = 0;
	const STATUS_UPDATE = 1;
	const STATUS_SUBMIT = 2;

	const ADMISSION_TYPE_EMERGENCY = 1;
	const ADMISSION_TYPE_URGENT = 2;
	const ADMISSION_TYPE_ELECTIVE = 3;
	const ADMISSION_TYPE_NEWBORN = 4;
	const ADMISSION_TYPE_TRAUMA = 5;
	const ADMISSION_TYPE_INFO_NOT_AVAIL = 9;

	const MOBILITY_AMBULATOR = 1;
	const MOBILITY_WHEELCHAIR = 2;
	const MOBILITY_AMBULANCE = 3;

	const PATIENTS_RELATIONS_SELF = 1;
	const PATIENTS_RELATIONS_AUTO_ACCIDENT = 2;
	const PATIENTS_RELATIONS_WORKERS_COMP = 3;
	const PATIENTS_RELATIONS_NOT_APPLICABLE = 4;

	const VERIFICATION_STATUS_BEGIN = 0;
	const VERIFICATION_STATUS_CONTINUE = 1;
	const VERIFICATION_STATUS_COMPLETE = 2;

	protected static $relationships_to_insured = [
		self::RELATIONSHIP_TO_INSURED_SELF => 'Self',
		self::RELATIONSHIP_TO_INSURED_HUSBAND => 'Husband',
		self::RELATIONSHIP_TO_INSURED_WIFE => 'Wife',
		self::RELATIONSHIP_TO_INSURED_PARENT => 'Parent',
		self::RELATIONSHIP_TO_INSURED_SIBLING => 'Sibling',
		self::RELATIONSHIP_TO_INSURED_CHILD => 'Child',
		self::RELATIONSHIP_TO_INSURED_OTHER => 'Other',
	    self::RELATIONSHIP_TO_INSURED_SPOUSE => 'Spouse',
	    self::RELATIONSHIP_TO_INSURED_EMPLOYEE => 'Employee',
	    self::RELATIONSHIP_TO_INSURED_UNKNOWN => 'Unknown',
	    self::RELATIONSHIP_TO_INSURED_ORGAN_DONOR => 'Organ Donor',
	    self::RELATIONSHIP_TO_INSURED_CADAVER_DONOR => 'Cadaver Donor',
	    self::RELATIONSHIP_TO_INSURED_LIFE_PARTNER => 'Life Partner',
	    self::RELATIONSHIP_TO_INSURED_OTHER_RELATIONSHIP => 'Other Relationship'
	];

	protected static $admissions_type = [
		self::ADMISSION_TYPE_EMERGENCY => 'Emergency',
		self::ADMISSION_TYPE_URGENT => 'Urgent',
		self::ADMISSION_TYPE_ELECTIVE => 'Elective',
		self::ADMISSION_TYPE_NEWBORN => 'Newborn',
		self::ADMISSION_TYPE_TRAUMA => 'Trauma',
		self::ADMISSION_TYPE_INFO_NOT_AVAIL => 'Information Not Available'
	];

	public $id_field = 'id';

	public $table = 'case_registration';

	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'case_id' => null,
		'patient_id' => null,
		'status' => 0,
		//patient details
		'title' => null,
		'first_name' => '',
		'middle_name' => '',
		'last_name' => '',
		'suffix' => null,
		'ssn' => null,
		'dob' => null,
		'gender' => null,
		'race' => null,
		'ethnicity' => null,
		'language_id' => null,
		'status_marital' => null,
		'status_employment' => null,
		'employer' => '',
		'employer_phone' => null,
		'additional_phone' => null,
		'point_of_contact_phone' => null,
		'point_of_contact_phone_type' => null,
		//contact
		'home_address' => '',
		'home_apt_number' => '',
		'home_state_id' => null,
		'custom_home_state' => null,
		'home_city_id' => null,
		'custom_home_city' => null,
		'home_zip_code' => '',
		'home_country_id' => null,
		'home_phone' => null,
		'home_email' => '',
		'ec_name' => '',
		'ec_relationship' => '',
		'ec_phone_number' => null,
		'ec_phone_type' => null,
		'home_phone_type' => null,
		'additional_phone_type' => null,
		'parents_name' => null,
		'relationship' => null,
		//specific
		'admission_type' => self::ADMISSION_TYPE_ELECTIVE,
		'patients_relations' => self::PATIENTS_RELATIONS_NOT_APPLICABLE,
		'auto_insurance_name' => '',
		'auto_adjust_name' => '',
		'auto_claim' => '',
		'auto_adjuster_phone' => null,
		'auto_insurance_address' => '',
		'auto_city_id' => null,
		'auto_state_id' => null,
		'auto_zip' => '',
		'auto_insurance_company_phone' => null,
		'auto_insurance_authorization_number' => null,
		'accident_date' => null,
		'attorney_name' => '',
		'attorney_phone' => null,
		'work_comp_insurance_name' => '',
		'work_comp_adjusters_name' => '',
		'work_comp_claim' => '',
		'work_comp_adjuster_phone' => null,
		'work_comp_insurance_address' => '',
		'work_comp_city_id' => null,
		'work_comp_state_id' => null,
		'work_comp_zip' => '',
		'work_comp_accident_date' => null,
		'work_comp_is_primary' => null,
		'work_comp_insurance_company_phone' => null,
		'work_comp_authorization_number' => null,
		'auto_is_primary' => null,

		'verification_status' => self::VERIFICATION_STATUS_BEGIN,
		'verification_completed_date' => null,
	];

	protected $belongs_to = [
		'organization' => [
			'model' => 'Organization',
			'key' => 'organization_id'
		],
		'case' => [
			'model' => 'Cases_Item',
			'key' => 'case_id'
		],
		'patient' => [
			'model' => 'Patient',
			'key' => 'patient_id'
		],
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
		'auto_state' => [
			'model' => 'Geo_State',
			'key' => 'auto_state_id'
		],
		'auto_city' => [
			'model' => 'Geo_City',
			'key' => 'auto_city_id'
		],
		'work_comp_state' => [
			'model' => 'Geo_State',
			'key' => 'work_comp_state_id'
		],
		'work_comp_city' => [
			'model' => 'Geo_City',
			'key' => 'work_comp_city_id'
		],
	];

	protected $has_one = [
		'patient_form_influenza' => [
			'model' => 'Patient_Appointment_Form_Influenza',
			'key' => 'case_registration_id',
			'cascade_delete' => true
		],
	];

	protected $has_many = [
		'insurances' => [
			'model' => 'Cases_Registration_Insurance',
			'key' => 'registration_id',
			'cascade_delete' => true
		],
		'documents' => [
			'model' => 'Cases_Registration_Document',
			'key' => 'case_registration_id',
			'cascade_delete' => true
		],
		'secondary_diagnosis' => [
			'model' => 'ICD',
			'through' => 'case_registration_secondary_diagnosis',
			'key' => 'reg_id',
			'foreign_key' => 'diagnosis_id'
		],
		'admitting_diagnosis' => [
			'model' => 'ICD',
			'through' => 'case_registration_admitting_diagnosis',
			'key' => 'reg_id',
			'foreign_key' => 'diagnosis_id'
		],
		'verifications' => [
			'model' => 'Cases_Registration_Insurance_Verification',
			'key' => 'case_registration_id',
			'cascade_delete' => true
		],
	];

	public function getAge()
	{
		$date = new \DateTime($this->dob);
		$now = new \DateTime();
		$interval = $now->diff($date);
		return $interval->y;
	}

	public function getGender()
	{
		$genders = \Opake\Model\Patient::getGenderList();
		return isset($genders[$this->gender]) ? $genders[$this->gender] : '';
	}

	/**
	 * @return string
	 */
	public function getFullNameWithMiddle()
	{
		return sprintf('%s, %s %s', $this->last_name, $this->first_name, $this->middle_name);
	}

	public function getRelationshipToInsured()
	{
		if (!is_null($this->relationship_to_insured)) {
			return self::$relationships_to_insured[$this->relationship_to_insured];
		}

		return null;
	}

	public function isLatestRegistrationForPatient()
	{
		$q = $this->conn->query('select')
			->table($this->table)
			->fields($this->id_field)
			->where('patient_id', $this->patient_id)
			->order_by($this->id_field, 'desc')
			->limit(1);

		$id = $q->execute()->get($this->id_field);
		return (!$id || $id == $this->id());
	}

	/**
	 * @deprecated
	 * @param null $key
	 * @return \Opake\Extentions\Validate
	 */
	public function getAutoInsuranceValidator($key = null)
	{
		$validator = parent::getValidator();
		return $validator;
	}

	/**
	 * @deprecated
	 * @param null $key
	 * @return \Opake\Extentions\Validate
	 */
	public function getWorkersCompValidator($key = null)
	{
		$validator = parent::getValidator();
		return $validator;
	}

	public function getFullName()
	{
		return sprintf('%s %s', $this->first_name, $this->last_name);
	}

	public function getFullNameForCalendarCell()
	{
		return sprintf('%s, %s', $this->last_name, $this->first_name);
	}

	public function getFullNameForLabel()
	{
		return substr($this->last_name . ', ' . $this->first_name, 0, 27);
	}

	public function getInsuredName()
	{
		return sprintf('%s %s %s', $this->first_name, $this->middle_name, $this->last_name);
	}

	public function getAdmitType()
	{
		$result = '';
		if ($this->admission_type && isset(self::$admissions_type[$this->admission_type])) {
			$result = self::$admissions_type[$this->admission_type];
		}
		return $result;
	}

	/**
	 * @return string
	 */
	public function getPrimaryInsuranceTitle()
	{
		$primaryInsurance = $this->getPrimaryInsurance();
		if (!$primaryInsurance) {
			return '';
		}
		return $primaryInsurance->getTitle();
	}

	/**
	 * @return integer
	 */
	public function getPrimaryInsuranceType()
	{
		$primaryInsurance = $this->insurances
			->where('deleted', 0)
			->where('order', 1)
			->order_by('order', 'ASC')
			->find();

		if (!$primaryInsurance->loaded()) {
			return null;
		}

		return $primaryInsurance->type;
	}

	/**
	 * @return \Opake\Model\Cases\Registration\Insurance
	 */
	public function getSecondaryInsurance()
	{
		$insurance = $this->insurances
			->where('deleted', 0)
			->where('order', 2)
			->order_by('order', 'ASC')
			->find();

		if (!$insurance->loaded()) {
			return null;
		}

		return $insurance;
	}

	/**
	 * @return integer
	 */
	public function getSecondaryInsuranceType()
	{
		$primaryInsurance = $this->insurances
			->where('deleted', 0)
			->where('order', 2)
			->order_by('order', 'ASC')
			->find();

		if (!$primaryInsurance->loaded()) {
			return null;
		}

		return $primaryInsurance->type;
	}

	public function fromPatient(\Opake\Model\Patient $patient)
	{
		$this->organization_id = $patient->organization_id;
		$this->title = $patient->title;
		$this->suffix = $patient->suffix;
		$this->first_name = $patient->first_name;
		$this->middle_name = $patient->middle_name;
		$this->last_name = $patient->last_name;
		$this->ssn = $patient->ssn;
		$this->gender = $patient->gender;
		$this->race = $patient->race;
		$this->dob = $patient->dob;
		$this->ethnicity = $patient->ethnicity;
		$this->language_id = $patient->language_id;
		$this->status_marital = $patient->status_marital;
		$this->status_employment = $patient->status_employment;
		$this->employer = $patient->employer;
		$this->employer_phone = $patient->employer_phone;
		$this->additional_phone = $patient->additional_phone;
		$this->point_of_contact_phone = $patient->point_of_contact_phone;
		$this->point_of_contact_phone_type = $patient->point_of_contact_phone_type;
		$this->patient_id = $patient->id;
		$this->home_address = $patient->home_address;
		$this->home_apt_number = $patient->home_apt_number;
		$this->home_state_id = $patient->home_state_id;
		$this->custom_home_state = $patient->custom_home_state;
		$this->home_city_id = $patient->home_city_id;
		$this->custom_home_city = $patient->custom_home_city;
		$this->home_zip_code = $patient->home_zip_code;
		$this->home_country_id = $patient->home_country_id;
		$this->home_phone = $patient->home_phone;
		$this->home_phone_type = $patient->home_phone_type;
		$this->home_email = $patient->home_email;
		$this->ec_name = $patient->ec_name;
		$this->ec_relationship = $patient->ec_relationship;
		$this->ec_phone_number = $patient->ec_phone_number;
		$this->home_phone_type = $patient->home_phone_type;
		$this->additional_phone_type = $patient->additional_phone_type;
		$this->ec_phone_type = $patient->ec_phone_type;
		$this->parents_name = $patient->parents_name;
		$this->relationship = $patient->relationship;
	}

	public function initInsurancesFromPatient(\Opake\Model\Patient $patient)
	{

	}

	public function fromArray($data)
	{
		if (isset($data->secondary_diagnosis) && $data->secondary_diagnosis) {
			$secondary_diagnosis = [];
			foreach ($data->secondary_diagnosis as $diagnosis) {
				$secondary_diagnosis[] = $diagnosis->id;
			}
			$data->secondary_diagnosis = $secondary_diagnosis;
		}

		if (isset($data->admitting_diagnosis) && $data->admitting_diagnosis) {
			$admitting_diagnosis = [];
			foreach ($data->admitting_diagnosis as $diagnosis) {
				$admitting_diagnosis[] = $diagnosis->id;
			}
			$data->admitting_diagnosis = $admitting_diagnosis;
		}

		if (isset($data->language) && $data->language) {
			$data->language_id = $data->language->id;
		}

		if (isset($data->home_country) && $data->home_country) {
			$data->home_country_id = $data->home_country->id;
		}
		if (isset($data->home_state) && $data->home_state) {
			$data->home_state_id = $data->home_state->id;
		}
		if (isset($data->home_city) && $data->home_city) {
			$data->home_city_id = $data->home_city->id;
		}
		if (isset($data->home_zip_code) && is_object($data->home_zip_code)) {
			$data->home_zip_code = $data->home_zip_code->code;
		}

		if (isset($data->dob) && $data->dob) {
			$data->dob = TimeFormat::formatToDB($data->dob);
		}

		if (isset($data->accident_date) && $data->accident_date) {
			$data->accident_date = TimeFormat::formatToDB($data->accident_date);
		}

		if (isset($data->auto_state) && $data->auto_state) {
			$data->auto_state_id = $data->auto_state->id;
		}
		if (isset($data->auto_city) && $data->auto_city) {
			$data->auto_city_id = $data->auto_city->id;
		}

		if (isset($data->work_comp_accident_date) && $data->work_comp_accident_date) {
			$data->work_comp_accident_date = TimeFormat::formatToDB($data->work_comp_accident_date);
		}

		if (isset($data->work_comp_state) && $data->work_comp_state) {
			$data->work_comp_state_id = $data->work_comp_state->id;
		}
		if (isset($data->work_comp_city) && $data->work_comp_city) {
			$data->work_comp_city_id = $data->work_comp_city->id;
		}

		$cityFields = [
			'home_city' => 'home_city_id',
			'auto_city' => 'auto_city_id',
			'work_comp_city' => 'work_comp_city_id'
		];

		foreach ($cityFields as $fieldName => $idFieldName) {
			if (property_exists($data, $fieldName)) {
				if (!empty($data->$fieldName->id)) {
					$data->$idFieldName = $data->$fieldName->id;
				} else if (!empty($data->$fieldName->name)) {
					$model = $this->pixie->orm->get('Geo_City');

					$organizationId = null;
					if (isset($data->organization->id)) {
						$organizationId = $data->organization->id;
					} else if (isset($data->organization_id)) {
						$organizationId = $data->organization_id;
					} else {
						throw new \Exception('Can\'t add new city without ID of organization');
					}

					$city = $model->addCustomRecord($organizationId, $data->$fieldName->state_id, $data->$fieldName->name);
					$data->$idFieldName = $city->id();
				} else if ($data->$fieldName === null) {
					$data->$idFieldName = null;
				}
				unset($data->$fieldName);
			}
		}

		return $data;
	}

	public function isAllSectionsValid()
	{
		return $this->isPatientDetailsSectionValid(false) && $this->isInsuranceSectionValid(false) && $this->isFormsSectionValid(false);
	}

	public function isPatientDetailsSectionValid($checkedForCompleted = true)
	{
		if ($this->isCompeted() && $checkedForCompleted) {
			return true;
		}

		return $this->getValidator()->valid();
	}

	public function isCompeted()
	{
		return $this->status == self::STATUS_SUBMIT;
	}

	public function getSelectedInsurances()
	{
		return $this->insurances
			->where('order', 'IS NOT NULL', $this->pixie->db->expr(''))
			->where('deleted', 0)
			->order_by('order', 'ASC')
			->find_all();
	}

	/**
	 * @return \Opake\Model\Cases\Registration\Insurance
	 */
	public function getPrimaryInsurance()
	{
		$insurance = $this->insurances
			->where('order', 'IS NOT NULL', $this->pixie->db->expr(''))
			->where('deleted', 0)
			->where('order', 1)
			->order_by('order', 'ASC')
			->find();

		return ($insurance->loaded()) ? $insurance : null;
	}

	public function getValidator($key = null)
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('first_name')->rule('filled')->error('You must specify First Name');
		$validator->field('last_name')->rule('filled')->error('You must specify Last Name');
		$validator->field('ssn')->rule('filled')->error('You must specify Social Security #');
		$validator->field('gender')->rule('filled')->error('You must specify Gender');
		$validator->field('race')->rule('filled')->error('You must specify Race');
		$validator->field('ethnicity')->rule('filled')->error('You must specify Ethnicity');
		$validator->field('language_id')->rule('filled')->error('You must specify Preferred Language');
		$validator->field('dob')->rule('filled')->error('You must specify Date of Birth');
		$validator->field('home_address')->rule('filled')->error('You must specify Home Address');
		$validator->field('status_marital')->rule('filled')->error('You must specify Marital Status');
		if ($this->home_country_id == 235) {
			$validator->field('home_city_id')->rule('filled')->error('You must specify Home City');
			$validator->field('home_zip_code')->rule('filled')->error('You must specify Home Zip code');
			$validator->field('home_state_id')->rule('filled')->error('You must specify Home State');
		} else {
			$validator->field('custom_home_city')->rule('filled')->error('You must specify Home City');
		}
		$validator->field('home_country_id')->rule('filled')->error('You must specify Home Country');
		$validator->field('home_phone')->rule('filled')->error('You must specify Phone #');
		$validator->field('ec_name')->rule('filled')->error('You must specify Emergency Contact Name');
		$validator->field('ec_relationship')->rule('filled')->error('You must specify Relationship to Patient');
		$validator->field('ec_phone_number')->rule('filled')->error('You must specify Emergency Phone #');

		$validator->field('patients_relations')->rule('filled_callback', function ($value) {
			return !empty($value);
		})->error('You must specify patient\'s condition relation');

		if ($key == 'filledValidationOnly') {
			return $validator;
		}

		$validator->field('ssn')->rule('numeric', $this)->error('The Social Security # field must be numeric');
		$validator->field('ssn')->rule('min_length', 9)->error('The Social Security # must be equal to 9 characters');
		$validator->field('ssn')->rule('max_length', 9)->error('The Social Security # must be equal to 9 characters');
		$validator->field('ssn')->rule('callback', function ($value) {
			$patient_valid = true;
			$patient = $this->pixie->orm->get('Patient')
				->where('ssn', $value)
				->where('id', '<>', $this->patient_id);
			if (isset($this->organization_id)) {
				$patient->where('organization_id', $this->organization_id);
			}
			$model = $patient->find();
			if ($model->loaded()) {
				$patient_valid = false;
			}
			return $patient_valid;
		})->error(sprintf('Patient with SSN %s already exists', $this->ssn));

		$validator->field('dob')->rule('date')->error('Incorrect Date of Birth format');
		$validator->field('home_phone')->rule('phone')->error('Incorrect Phone #');
		$validator->field('home_email')->rule('email')->error('Invalid Email Address');
		$validator->field('ec_phone_number')->rule('phone')->error('Incorrect Emergency Phone # format');
		$validator->field('employer_phone')->rule('phone')->error('Incorrect Employer Phone # format');
		$validator->field('additional_phone')->rule('phone')->error('Incorrect Additional Phone # format');
		$validator->field('point_of_contact_phone')->rule('phone')->error('Incorrect Point of Contact Phone # format');


		return $validator;
	}

	public function isInsuranceSectionValid($checkedForCompleted = true)
	{
		if ($this->isCompeted() && $checkedForCompleted) {
			return true;
		}

		$insurances = $this->insurances
			->where('deleted', 0)
			->find_all()
			->as_array();

		foreach ($insurances as $insurance) {
			if (!$insurance->getValidator()->valid()) {
				return false;
			}
		}

		return true;
	}

	public function isFormsSectionValid($checkedForCompleted = true)
	{
		if ($this->isCompeted() && $checkedForCompleted) {
			return true;
		}

		foreach ($this->documents->find_all() as $doc) {
			if (!$doc->status) {
				return false;
			}
		}

		return true;
	}

	public function addDocument($docTypeId, $file)
	{
		$this->removeDocument($docTypeId);

		$docModel = $this->documents->where('document_type', $docTypeId)->find();
		if (!$docModel->loaded()) {
			$docModel = $this->pixie->orm->get('Cases_Registration_Document');
			$docModel->case_registration_id = $this->id();
			$docModel->document_type = $docTypeId;
		}

		$docModel->uploaded_file_id = $file->id();
		$docModel->uploaded_date = strftime(TimeFormat::DATE_FORMAT_DB, time());
		$docModel->status = 1;

		$docModel->save();

		return $docModel;
	}

	public function removeDocument($docTypeId)
	{
		$documents = $this->documents->where('document_type', $docTypeId)
			->find_all();


		$docTypeModel = $this->pixie->orm->get('Forms_Document');
		$docTypes = $docTypeModel->getFormsForCase($this->case);
		$formsTypes = [];
		foreach ($docTypes as $form) {
			$formsTypes[] = $form->doc_type_id;
		}

		if ($documents) {
			foreach ($documents as $document) {
				$file = $document->file;
				if ($file && $file->loaded()) {
					$file->removeFile();
					$file->delete();
				}

				if (!in_array($document->document_type, $formsTypes)) {
					$document->delete();
				} else {
					$document->status = 0;
					$document->uploaded_file_id = null;
					$document->remote_file_id = null;
					$document->save();
				}
			}
		}
	}

	public function initForms()
	{
		//todo: temporarily disabled
		/*$docTypeModel = $this->pixie->orm->get('Forms_Document');
		$docTypes = $docTypeModel->getFormsForCase($this->case);

		foreach ($docTypes as $docType) {
			$docModel = $this->pixie->orm->get('Cases_Registration_Document');
			$docModel->case_registration_id = $this->id;
			$docModel->document_type = $docType->doc_type_id;
			$docModel->uploaded_date = strftime(TimeFormat::DATE_FORMAT_DB, time());
			$docModel->status = 0;
			$docModel->save();
		}*/
	}

	public function updateForms()
	{
		//todo: temporarily disabled
		/*$docTypeModel = $this->pixie->orm->get('Forms_Document');
		$docTypes = $docTypeModel->getFormsForCase($this->case);

		$newDocTypes = [];
		foreach ($docTypes as $docType) {
			if ($docType->doc_type_id) {
				$newDocTypes[] = $docType->doc_type_id;
			}
		}

		$usedDocTypes = [];
		foreach ($this->documents->find_all() as $document) {
			$usedDocTypes[] = $document->document_type;

			if (!$document->status) {
				if (!in_array($document->document_type, $newDocTypes)) {
					$document->delete();
				}
			}
		}

		foreach ($newDocTypes as $docTypeId) {
			if (!in_array($docTypeId, $usedDocTypes)) {
				$docModel = $this->pixie->orm->get('Cases_Registration_Document');
				$docModel->case_registration_id = $this->id();
				$docModel->document_type = $docTypeId;
				$docModel->uploaded_date = strftime(TimeFormat::DATE_FORMAT_DB, time());
				$docModel->status = 0;
				$docModel->save();
			}
		}*/
	}

	public function updateVerificationStatus()
	{
		$verified = false;
		$verifications = $this->verifications->find_all();
		/** @var Verification $verification */
		foreach ($verifications as $verification) {
			if ($verification->isVerified()) {
				$verified = true;
			}
		}

		if ($verified && $this->isVerified()) {
			return;
		}
		else {
			$this->verification_status = $verified ?
				static::VERIFICATION_STATUS_COMPLETE : static::VERIFICATION_STATUS_CONTINUE;
			if ($verified) {
				$this->verification_completed_date = TimeFormat::formatToDBDatetime(new \DateTime());
			}
		}
	}

	public function isVerified()
	{
		return $this->verification_status == static::VERIFICATION_STATUS_COMPLETE;
	}

	public function toShortArray()
	{
		return [
			'id' => $this->id,
			'first_name' => $this->first_name,
			'last_name' => $this->last_name,
			'status' => $this->status,
			'patient' => $this->patient->toShortArray(),
			'case' => [
				'id' => $this->case->id,
				'type' => $this->case->type->toArray(),
				'time_start' => date('D M d Y H:i:s O', strtotime($this->case->time_start)),
				'appointment_status' => $this->case->appointment_status
			]
		];
	}

	public function toArray()
	{
		$insurances = [];
		foreach ($this->insurances->where('deleted', 0)->find_all() as $insurance) {
			$insurances[] = $insurance->toArray();
		}

		$secondary_diagnosis = [];
		foreach ($this->secondary_diagnosis->find_all()->as_array() as $diagnosis) {
			$secondary_diagnosis[] = $diagnosis->toArray();
		}

		$data = [
			'id' => $this->id(),
			'organization_id' => $this->organization_id,
			'display_point_of_contact' => (bool)$this->organization->sms_template->poc_sms,
			'dob' => $this->dob,
			'time_start' => date('D M d Y H:i:s O', strtotime($this->case->time_start)),
			'time_end' => date('D M d Y H:i:s O', strtotime($this->case->time_end)),
			//'case' => $this->case->toArray(),
			'patient' => $this->patient->toShortArray(),
			'insurances' => $insurances,
			'is_patient_details_valid' => $this->isPatientDetailsSectionValid(),
			'is_forms_valid' => $this->isFormsSectionValid(),
			'is_insurance_valid' => $this->isInsuranceSectionValid(),
			'documents' => $this->getDocumentsArray(),
			'secondary_diagnosis' => $secondary_diagnosis,
			'home_country' => $this->home_country && $this->home_country->loaded() ? $this->home_country->toArray() : null,
			'home_state' => $this->home_state && $this->home_state->loaded() ? $this->home_state->toArray() : null,
			'home_city' => $this->home_city && $this->home_city->loaded() ? $this->home_city->toArray() : null,
			'auto_state' => $this->auto_state && $this->auto_state->loaded() ? $this->auto_state->toArray() : null,
			'auto_city' => $this->auto_city && $this->auto_city->loaded() ? $this->auto_city->toArray() : null,
			'work_comp_state' => $this->work_comp_state && $this->work_comp_state->loaded() ? $this->work_comp_state->toArray() : null,
			'work_comp_city' => $this->work_comp_city && $this->work_comp_city->loaded() ? $this->work_comp_city->toArray() : null,
			'case_id' => $this->case_id,
			'patient_id' => $this->patient_id,
			'status' => $this->status,
			'title' => $this->title,
			'first_name' => $this->first_name,
			'middle_name' => $this->middle_name,
			'last_name' => $this->last_name,
			'suffix' => $this->suffix,
			'ssn' => $this->ssn,
			'gender' => $this->gender,
			'race' => $this->race,
			'ethnicity' => $this->ethnicity,
			'language_id' => $this->language_id,
			'status_marital' => $this->status_marital,
			'status_employment' => $this->status_employment,
			'employer' => $this->employer,
			'employer_phone' => $this->employer_phone,
			'additional_phone' => $this->additional_phone,
			'point_of_contact_phone' => $this->point_of_contact_phone,
			'point_of_contact_phone_type' => $this->point_of_contact_phone_type,
			'home_address' => $this->home_address,
			'home_apt_number' => $this->home_apt_number,
			'custom_home_state' => $this->custom_home_state,
			'custom_home_city' => $this->custom_home_city,
			'home_zip_code' => $this->home_zip_code,
			'home_phone' => $this->home_phone,
			'home_email' => $this->home_email,
			'ec_name' => $this->ec_name,
			'ec_relationship' => $this->ec_relationship,
			'ec_phone_number' => $this->ec_phone_number,
			'admission_type' => $this->admission_type,
			'patients_relations' => $this->patients_relations,
			'auto_insurance_name' => $this->auto_insurance_name,
			'auto_adjust_name' => $this->auto_adjust_name,
			'auto_claim' => $this->auto_claim,
			'auto_adjuster_phone' => $this->auto_adjuster_phone,
			'auto_insurance_company_phone' => $this->auto_insurance_company_phone,
			'auto_insurance_authorization_number' => $this->auto_insurance_authorization_number,
			'auto_insurance_address' => $this->auto_insurance_address,
			'auto_state_id' => $this->auto_state_id,
			'auto_zip' => $this->auto_zip,
			'accident_date' => $this->accident_date,
			'attorney_name' => $this->attorney_name,
			'attorney_phone' => $this->attorney_phone,
			'work_comp_insurance_name' => $this->work_comp_insurance_name,
			'work_comp_adjusters_name' => $this->work_comp_adjusters_name,
			'work_comp_claim' => $this->work_comp_claim,
			'work_comp_adjuster_phone' => $this->work_comp_adjuster_phone,
			'work_comp_insurance_address' => $this->work_comp_insurance_address,
			'work_comp_zip' => $this->work_comp_zip,
			'work_comp_accident_date' => $this->work_comp_accident_date,
			'work_comp_is_primary' => $this->work_comp_is_primary,
			'work_comp_insurance_company_phone' => $this->work_comp_insurance_company_phone,
			'work_comp_authorization_number' => $this->work_comp_authorization_number,
			'auto_is_primary' => $this->auto_is_primary,
		    'home_phone_type' => $this->home_phone_type,
		    'additional_phone_type' => $this->additional_phone_type,
		    'ec_phone_type' => $this->ec_phone_type,
		    'parents_name' => $this->parents_name
		];

		if ($user = $this->pixie->auth->user()) {
			$data['is_self_for_user'] = $this->isSelf($user);
		}

		return $data;
	}

	public function getDocumentsArray()
	{
		$result = [];

		$service = $this->pixie->services->get('cases');
		$documents = $this->documents->with('type')->find_all();

		foreach ($documents as $document) {
			$typeId = $document->document_type;
			if ($typeId) {
				$doc = $document->toArray();
				$doc['countExistedDocuments'] = count($service->getExistedDocs($this->case, $typeId)->as_array());
				$result[] = $doc;
			}
		}

		return $result;
	}

	public function isSelf($user)
	{
		if (!$this->case || !$this->case->loaded()) {
			return false;
		}

		return $this->case->isSelf($user);
	}

	public function complete()
	{
		$this->conn->query('update')->table($this->table)
			->data(['status' => self::STATUS_SUBMIT])
			->where('id', $this->id)
			->execute();
	}

	public function reopen()
	{
		$this->conn->query('update')->table($this->table)
			->data(['status' => self::STATUS_UPDATE])
			->where('id', $this->id)
			->execute();

	}

	/**
	 * Return city name
	 * @return string
	 */
	public function getHomeCityName()
	{
		if ($this->home_country_id == 235) {
			return $this->home_city->name;
		} else {
			return $this->custom_home_city;
		}
	}

	public function getCoverageTypeName()
	{
		$names = \Opake\Model\Patient::getCoverageTypes();
		if ($this->coverage_type && isset($names[$this->coverage_type])) {
			return $names[$this->coverage_type];
		}

		return '';
	}

	public static function getRelationshipInsuredList()
	{
		return self::$relationships_to_insured;
	}

	public static function getAdmissionTypesList()
	{
		return [
			1 => 'Emergency',
			2 => 'Urgent',
			3 => 'Elective',
			4 => 'Newborn',
			5 => 'Trauma',
			9 => 'Information Not Available'
		];
	}

	public static function getPatientRelationsList()
	{
		return [
			0 => 'None',
			1 => 'Self',
			2 => 'Auto Accident / No-Fault',
			3 => 'Workers Comp',
			4 => 'Not Applicable'
		];
	}

	public static function getReimbursment()
	{
		return [
			1 => 'UCR-Based',
			2 => 'Medicare-Based'
		];
	}
}
