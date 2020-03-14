<?php

namespace Opake\Model;

class BookingSheetTemplate extends AbstractModel
{
	const TYPE_DEFAULT = 1;
	const TYPE_CUSTOM = 2;

	const FORM_PATIENT_INFO = 1;
	const FORM_CASE_INFO = 2;

	const FIELD_PATIENT_NAME = 1;
	const FIELD_MI = 2;
	const FIELD_SUFFIX = 3;
	const FIELD_IF_MINOR = 4;
	const FIELD_ADDRESS = 5;
	const FIELD_APT = 6;
	const FIELD_STATE = 7;
	const FIELD_CITY = 8;
	const FIELD_ZIP = 9;
	const FIELD_COUNTRY = 10;
	const FIELD_PHONE = 11;
	const FIELD_ADDITIONAL_PHONE = 12;
	const FIELD_EMAIL = 13;
	const FIELD_DATE_OF_BIRTH = 14;
	const FIELD_SSN = 15;
	const FIELD_GENDER = 16;
	const FIELD_MARTIAL_STATUS = 17;
	const FIElD_EMERGENCY_CONTACT_RELATIONSHIP = 18;
	const FIELD_EMERGENCY_PHONE = 19;

	const FIELD_SURGEON = 20;
	const FIELD_SURGEON_ASSISTANT = 21;
	const FIELD_OTHER_STAFF = 22;
	const FIELD_PRIOR_AUTHORIZATION_NUMBER = 23;
	const FIELD_ADMISSION_TYPE = 24;
	const FIELD_ROOM = 25;
	const FIELD_POINT_OF_ORIGIN = 26;
	const FIELD_DATE_OF_SERVICE = 27;
	const FIELD_TIME_START = 28;
	const FIELD_CASE_LENGTH = 29;
	const FIELD_PATIENT_EMPLOYED = 30;
	const FIELD_PROPOSED_PROCEDURE_CODES = 31;
	const FIELD_LOCATION = 32;
	const FIELD_DATE_OF_INJURY = 33;
	const FIELD_PRIMARY_DIAGNOSIS = 34;
	const FIELD_SECONDARY_DIAGNOSIS = 35;
	const FIELD_PRE_OP_DATA_REQUIRED = 36;
	const FIELD_STUDIES_ORDERED = 37;
	const FIELD_ANESTHESIA_TYPE = 38;
	const FIELD_SPECIAL_EQUIPMENT = 39;
	const FIELD_TRANSPORT = 40;
	const FIELD_IMPLANTS = 41;
	const FIELD_DESCRIPTION = 42;
	const FIELD_POINT_OF_ORIGIN_NPI = 43;
	const FIELD_POINT_OF_ORIGIN_PROVIDER = 44;
	const FIELD_POINT_OF_CONTACT_SMS = 45;

	public $id_field = 'id';
	public $table = 'booking_sheet_template';
	protected $_row = [
		'id' => null,
		'organization_id' => null,
		'type' => null,
		'name' => null,
		'is_all_sites' => 0
	];

	protected $belongs_to = [
		'organization' => [
			'model' => 'Organization',
			'key' => 'organization_id'
		],
	];

	protected $has_many = [
		'sites' => [
			'model' => 'Site',
			'through' => 'booking_sheet_template_site',
			'key' => 'booking_sheet_template_id',
			'foreign_key' => 'site_id',
			'overwrite' => [
				'replace' => true,
				'ordering' => true
			]
		],
	    'fields' => [
		    'model' => 'BookingSheetTemplate_Field',
	        'key' => 'booking_sheet_template_id',
	        'cascade_delete' => true
	    ]
	];

	protected $formatters = [
		'List' => [
			'class' => '\Opake\Formatter\BookingSheetTemplate\ListFormatter'
		],
	    'Template' => [
		    'class' => '\Opake\Formatter\BookingSheetTemplate\TemplateFormatter'
	    ],
		'TemplateBookingSheet' => [
			'class' => '\Opake\Formatter\BookingSheetTemplate\TemplateFormatter',
		    'onlyActive' => true
		]
	];

	public function updateFields($fields)
	{
		$this->fields->delete_all();

		foreach ($fields as $fieldId => $fieldData) {
			$fieldModel = $this->pixie->orm->get('BookingSheetTemplate_Field');
			$fieldModel->booking_sheet_template_id = $this->id();
			$fieldModel->field = $fieldId;
			$fieldModel->x = $fieldData->x;
			$fieldModel->y = $fieldData->y;
			$fieldModel->active = ($fieldData->active) ? 1 : 0;
			$fieldModel->save();
		}
	}

	public function createDefaultFields()
	{
		$defaultFields = static::getDefaultFieldsConfig();
		foreach ($defaultFields as $fieldId => $fieldData) {
			$fieldModel = $this->pixie->orm->get('BookingSheetTemplate_Field');
			$fieldModel->booking_sheet_template_id = $this->id();
			$fieldModel->field = $fieldId;
			$fieldModel->x = $fieldData['x'];
			$fieldModel->y = $fieldData['y'];
			$fieldModel->active = ($fieldData['active']) ? 1 : 0;
			$fieldModel->save();
		}
	}

	public function getSiteIds()
	{
		$rows = $this->pixie->db->query('select')
			->table('booking_sheet_template_site')
			->fields('site_id')
			->where('booking_sheet_template_id', $this->id())
			->execute();

		$siteIds = [];
		foreach ($rows as $row) {
			$siteIds[] = (int) $row->site_id;
		}

		return $siteIds;
	}

	public static function getDefaultFieldsConfig()
	{
		$config = [
			self::FIELD_PATIENT_NAME => [
				'form' => self::FORM_PATIENT_INFO,
			    'height' => 1,
			    'width' => 6,
			    'x' => 0,
			    'y' => 0,
			    'title' => 'Patient Name',
			    'required' => true
			],
		    self::FIELD_MI => [
			    'form' => self::FORM_PATIENT_INFO,
			    'height' => 1,
			    'width' => 2,
			    'x' => 6,
			    'y' => 0,
		        'title' => 'MI'
		    ],
			self::FIELD_SUFFIX => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 2,
				'x' => 8,
				'y' => 0,
			    'title' => 'Suffix'
			],
			self::FIELD_IF_MINOR => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 2,
				'x' => 10,
				'y' => 0,
			    'title' => 'If Minor, Patient\'s Name'
			],
			self::FIELD_ADDRESS => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 0,
				'y' => 1,
				'title' => 'Address',
				'required' => true
			],
			self::FIELD_APT => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 1,
				'x' => 3,
				'y' => 1,
			    'title' => 'Apt'
			],
			self::FIELD_STATE => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 2,
				'x' => 4,
				'y' => 1,
				'title' => 'State',
				'required' => true
			],
			self::FIELD_CITY => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 2,
				'x' => 6,
				'y' => 1,
				'title' => 'City',
				'required' => true
			],
			self::FIELD_ZIP => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 2,
				'x' => 8,
				'y' => 1,
				'title' => 'Zip'
			],
			self::FIELD_COUNTRY => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 2,
				'x' => 10,
				'y' => 1,
			    'title' => 'Country',
			],
		    self::FIELD_PHONE => [
			    'form' => self::FORM_PATIENT_INFO,
			    'height' => 1,
			    'width' => 6,
			    'x' => 0,
			    'y' => 2,
		        'title' => 'Phone / Type'
		    ],
			self::FIELD_ADDITIONAL_PHONE => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 6,
				'x' => 6,
				'y' => 2,
			    'title' => 'Additional Phone / Type'
			],
			self::FIELD_EMAIL => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 6,
				'x' => 0,
				'y' => 3,
			    'title' => 'Email'
			],
			self::FIELD_POINT_OF_CONTACT_SMS => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 6,
				'x' => 6,
				'y' => 3,
				'title' => 'Point of Contact SMS / Type',
				'required' => true
			],
			self::FIELD_DATE_OF_BIRTH => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 0,
				'y' => 4,
			    'title' => 'Date of Birth',
			    'required' => true
			],
			self::FIELD_SSN => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 3,
				'y' => 4,
			    'title' => 'Social Security Number'
			],
			self::FIELD_GENDER => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 6,
				'y' => 4,
				'title' => 'Gender',
				'required' => true
			],
			self::FIELD_MARTIAL_STATUS => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 9,
				'y' => 4,
			    'title' => 'Martial Status'
			],
		    self::FIElD_EMERGENCY_CONTACT_RELATIONSHIP => [
			    'form' => self::FORM_PATIENT_INFO,
			    'height' => 1,
			    'width' => 6,
			    'x' => 0,
			    'y' => 5,
			    'title' => 'Emergency Contact / Relationship'
		    ],
			self::FIELD_EMERGENCY_PHONE => [
				'form' => self::FORM_PATIENT_INFO,
				'height' => 1,
				'width' => 6,
				'x' => 6,
				'y' => 5,
				'title' => 'Emergency Phone / Type'
			],
			self::FIELD_SURGEON => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 0,
				'y' => 0,
				'title' => 'Surgeon',
			    'required' => true
			],
			self::FIELD_SURGEON_ASSISTANT => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 3,
				'y' => 0,
				'title' => 'Surgeon Assistant'
			],
			self::FIELD_OTHER_STAFF => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 6,
				'y' => 0,
				'title' => 'Other Staff',
			    'required' => false
			],
			self::FIELD_ADMISSION_TYPE => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 0,
				'y' => 1,
				'title' => 'Admission Type'
			],
			self::FIELD_ROOM => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 3,
				'y' => 1,
				'title' => 'Room'
			],
			self::FIELD_POINT_OF_ORIGIN => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 6,
				'y' => 1,
				'title' => 'Point of Origin'
			],
			self::FIELD_POINT_OF_ORIGIN_PROVIDER => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 9,
				'y' => 1,
				'title' => 'Point of Origin Provider Name'
			],
			self::FIELD_DATE_OF_SERVICE => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 0,
				'y' => 2,
			    'title' => 'Date of Service'
			],
			self::FIELD_TIME_START => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 3,
				'y' => 2,
			    'title' => 'Time Start'
			],
			self::FIELD_CASE_LENGTH => [
				'form' => self::FORM_CASE_INFO,
			    'height' => 1,
			    'width' => 3,
			    'x' => 6,
			    'y' => 2,
			    'title' => 'Length of Case'
			],
			self::FIELD_POINT_OF_ORIGIN_NPI => [
				'form' => self::FORM_CASE_INFO,
			    'height' => 1,
			    'width' => 3,
			    'x' => 9,
			    'y' => 2,
			    'title' => 'Point of Origin NPI'
			],
			self::FIELD_PATIENT_EMPLOYED => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 12,
				'x' => 0,
				'y' => 3,
			    'title' => 'Is Patient Employed? / Dates unable to work'
			],
			self::FIELD_PROPOSED_PROCEDURE_CODES => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 6,
				'x' => 0,
				'y' => 4,
			    'title' => 'Proposed Procedure Codes',
			    //'required' => true
			],
			self::FIELD_LOCATION => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 6,
				'y' => 4,
			    'title' => 'Location'
			],
			self::FIELD_DATE_OF_INJURY => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 3,
				'x' => 9,
				'y' => 4,
			    'title' => 'Date of Injury'
			],
			self::FIELD_PRIMARY_DIAGNOSIS => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 6,
				'x' => 0,
				'y' => 5,
			    'title' => 'Primary Diagnosis',
			    //'required' => true
			],
			self::FIELD_SECONDARY_DIAGNOSIS => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 6,
				'x' => 6,
				'y' => 5,
			    'title' => 'Secondary Diagnosis'
			],
			self::FIELD_PRE_OP_DATA_REQUIRED => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 6,
				'x' => 0,
				'y' => 6,
				'title' => 'Pre-Op Data Required'
			],
			self::FIELD_STUDIES_ORDERED => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 6,
				'x' => 6,
				'y' => 6,
			    'title' => 'Studies Ordered'
			],
			self::FIELD_ANESTHESIA_TYPE => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 6,
				'x' => 0,
				'y' => 7,
			    'title' => 'Anasthesia Type'
			],
			self::FIELD_SPECIAL_EQUIPMENT => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 6,
				'x' => 6,
				'y' => 7,
			    'title' => 'Special Equipment'
			],
			self::FIELD_TRANSPORT => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 6,
				'x' => 0,
				'y' => 8,
			    'title' => 'Transport'
			],
			self::FIELD_IMPLANTS => [
				'form' => self::FORM_CASE_INFO,
				'height' => 1,
				'width' => 6,
				'x' => 6,
				'y' => 8,
			    'title' => 'Implants'
			],
		    self::FIELD_DESCRIPTION => [
			    'form' => self::FORM_CASE_INFO,
			    'height' => 2,
			    'width' => 12,
			    'x' => 0,
			    'y' => 9,
		        'title' => 'Description'
		    ]
		];

		foreach ($config as $field => $fieldConfig) {
			$config[$field]['active'] = true;
		}

		return $config;
	}

	public static function createDefaultBookingSheetTemplate()
	{
		$app = \Opake\Application::get();
		$model = $app->orm->get('BookingSheetTemplate');
		$model->type = self::TYPE_DEFAULT;
		$model->name = 'Default Booking Sheet';
		$model->is_all_sites = 1;

		return $model;
	}

	public static function getAvailableTemplatesForUser($user)
	{
		$app = \Opake\Application::get();
		$sites = $user->getSites();
		$siteIds = [];
		foreach ($sites as $site) {
			$siteIds[] = (int) $site->id();
		}

		$defaultTemplate = $app->orm->get('BookingSheetTemplate')
			->where('organization_id', $user->organization->id())
			->where('type', BookingSheetTemplate::TYPE_DEFAULT)
			->find();
		if (!$defaultTemplate->loaded()) {
			$defaultTemplate = BookingSheetTemplate::createDefaultBookingSheetTemplate();
			$defaultTemplate->organization_id = $user->organization->id();
			$defaultTemplate->save();
			$defaultTemplate->createDefaultFields();
		}

		if (!$siteIds) {
			return [$defaultTemplate];
		}

		$availableTemplates = [];
		$organizationTemplates = $app->orm->get('BookingSheetTemplate')
			->where('organization_id', $user->organization->id())
			->order_by('type')
			->order_by('id')
			->find_all();

		foreach ($organizationTemplates as $template) {
			if ($template->is_all_sites) {
				$availableTemplates[] = $template;
			} else {
				$templateSiteIds = $template->getSiteIds();
				foreach ($templateSiteIds as $templateSiteId) {
					if (in_array($templateSiteId, $siteIds)) {
						$availableTemplates[] = $template;
						break;
					}
				}
			}
		}

		if (!$availableTemplates) {
			return [$defaultTemplate];
		}

		return $availableTemplates;
	}

	public static function getDefaultTemplateForOrganization($organization)
	{
		$app = \Opake\Application::get();

		$defaultTemplate = $app->orm->get('BookingSheetTemplate')
			->where('organization_id', $organization->id())
			->where('type', BookingSheetTemplate::TYPE_DEFAULT)
			->find();

		if (!$defaultTemplate->loaded()) {
			$defaultTemplate = BookingSheetTemplate::createDefaultBookingSheetTemplate();
		}

		return $defaultTemplate;
	}
}