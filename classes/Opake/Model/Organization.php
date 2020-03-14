<?php

namespace Opake\Model;

use Opake\Helper\Config;
use Opake\Helper\TimeFormat;

class Organization extends AbstractModel
{

	public $id_field = 'id';
	public $table = 'organization';
	protected $_row = [
		'id' => null,
		'name' => null,
		'time_create' => null,
		'status' => self::DEFAULT_STATUS,
		'address' => '',
		'country_id' => null,
		'website' => '',
		'contact_name' => '',
		'contact_email' => '',
		'contact_phone' => '',
		'logo_id' => null,
		'comment' => '',
		'federal_tax' => '',
		'accept_assignment_payer_program' => null,
		'npi' => '',
		'nuance_org_id' => '',
		'eligible_service_codes' => '',
		'chargeable' => 0
	];

	protected $formatters = [
		'SelectOptions' => [
			'class' => '\Opake\Formatter\Organization\SelectOptionsFormatter'
		]
	];

	protected $belongs_to = [
		'country' => [
			'model' => 'Geo_Country',
			'key' => 'country_id'
		],
		'logo' => [
			'model' => 'UploadedFile_Image',
			'key' => 'logo_id'
		]
	];

	protected $has_one = [
		'portal' => [
			'model' => 'Patient_Portal',
			'key' => 'organization_id'
		],
		'sms_template' => [
			'model' => 'SmsTemplate',
			'key' => 'organization_id'
		]
	];

	protected $has_many = [
		'sites' => [
			'model' => 'site',
			'foreign_key' => 'site_id',
			'cascade_delete' => true
		],
		'users' => [
			'model' => 'user',
			'key' => 'organization_id',
			'cascade_delete' => true
		],
		'permissions' => [
			'model' => 'Organization_Permission',
			'key' => 'organization_id',
			'cascade_delete' => true
		],
		'practice_groups' => [
			'model' => 'PracticeGroup',
			'through' => 'organization_practice_groups',
			'key' => 'organization_id',
			'foreign_key' => 'practice_group_id'
		],
	];

	const STATUS_ACTIVE = 'active';
	const STATUS_INACTIVE = 'inactive';
	const DEFAULT_STATUS = self::STATUS_ACTIVE;

	public function getValidator()
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();
		$validator->field('name')->rule('filled')->rule('min_length', 2)->error('Invalid Name');
		$validator->field('name')->rule('unique', $this)->error(sprintf('Organization with name %s already exists', $this->name));
		$validator->field('contact_phone')->rule('phone')->error('Invalid Contact Phone');
		$validator->field('npi')->rule('min_length', 10)->error('Length of NPI should be equal to 10');
		$validator->field('npi')->rule('max_length', 10)->error('Length of NPI should be equal to 10');
		$validator->field('npi')->rule('callback', function ($value) {
			return (bool) preg_match('#^[0-9]*$#', $value);
		})->error('NPI must contain only numeric symbols');
		return $validator;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getLogo($size = NULL)
	{
		if ($logo = $this->getLogoModel()) {
			return $logo->getThumbnailWebPath($size);
		}

		if ($size) {
			return '/i/default-logo_' . $size . '.png';
		}

		return '/i/default-logo.png';
	}

	public function save()
	{
		if ($this->time_create == NULL) {
			$this->time_create = strftime('%Y-%m-%d %H:%M:%S');
		}
		if (!$this->status) {
			$this->status = self::DEFAULT_STATUS;
		}
		parent::save();
	}

	public function getSitesCount($activeState = true)
	{
		if (!$this->id()) {
			return 0;
		}
		if ($activeState) {
			return $this->sites->count_all();
		} else {
			return 0;
		}
	}

	public function getLocations()
	{
		$model = $this->pixie->orm->get('Location');
		$model->query->fields($model->table . '.*')
			->join(['site', 's'], ['location.site_id', 's.id'])
			->where('s.active', 1);
		if ($this->loaded()) {;
			$model->where('s.organization_id', $this->id);
		}
		return $model->find_all();
	}

	public function getUsersCount($activeState = true)
	{
		if (!$this->id()) {
			return 0;
		}
		if ($activeState) {
			return $this->users->where('status', User::STATUS_ACTIVE)->count_all();
		} else {
			return $this->users->where('status', User::STATUS_INACTIVE)->count_all();
		}
	}

	public function updatePermissions($permissions)
	{
		$this->permissions->delete_all();

		foreach ($permissions as $permissionName => $allowed) {
			$permission = $this->pixie->orm->get('Organization_Permission');
			$permission->organization_id = $this->id();
			$permission->permission = $permissionName;
			$permission->allowed = (bool)$allowed;
			$permission->save();
		}
	}

	public function toArray()
	{
		/* todo: move to opakeadmin */
		$orgPermissions = new \Opake\Permissions\Organization\OrganizationLevel($this);


		$practiceGroups = [];
		$practiceGroupIds = [];
		foreach ($this->practice_groups->find_all() as $practiceGroup) {
			$practiceGroupIds[] = (int) $practiceGroup->id();
			$practiceGroups[] = $practiceGroup->toArray();
		}

		$data = [
			'id' => (int)$this->id,
			'name' => $this->name,
			'logo_src' => $this->getLogo('default'),
			'logo_id' => $this->logo_id,
			'time_create' => TimeFormat::getDateTime($this->time_create),
			'status' => $this->status,
			'federal_tax' => $this->federal_tax,
			'accept_assignment_payer_program' => $this->accept_assignment_payer_program,
			'npi' => $this->npi,
			'address' => $this->address,
			'country' => ($this->country && $this->country->loaded()) ? $this->country->toArray() : null,
			'country_id' => $this->country_id,
			'website' => $this->website,
			'comment' => $this->comment,
			'contact_name' => $this->contact_name,
			'contact_email' => $this->contact_email,
			'contact_phone' => $this->contact_phone,
			'permissions' => [
				'settings' => $orgPermissions->getOrganizationPermissions(),
				'hierarchy' => $orgPermissions->getPermissionsHierarchy()
			],
			'practice_group_ids' => $practiceGroupIds,
			'practice_groups' => $practiceGroups,
			'nuance_org_id' => $this->nuance_org_id,
			'eligible_service_codes' => $this->eligible_service_codes,
			'display_point_of_contact' => (bool) $this->sms_template->poc_sms
		];

		return $data;
	}

	public function toShortArray()
	{
		return [
			'id' => (int)$this->id,
			'name' => $this->name,
			'time_create' => date('D M d Y H:i:s O', strtotime($this->time_create)),
			'status' => $this->status,
			'sites_count' => $this->sites_count,
			'users_count' => $this->users_count
		];
	}

	public function updateChargeable($chargePrice)
	{
		$this->conn->query('update')->table($this->table)
			->data(['chargeable' => $chargePrice])
			->where('id', $this->id)
			->execute();
	}

	/**
	 * @return \Opake\Model\UploadedFile\Image
	 */
	protected function getLogoModel()
	{
		if ($this->logo_id) {
			if ($this->logo->loaded()) {
				return $this->logo;
			}
			if (!$this->logo->loaded() && $this->logo_id) {
				$model = $this->pixie->orm->get('UploadedFile_Image', $this->logo_id);
				if ($model->loaded()) {
					return $model;
				}
			}
		}

		return null;
	}
}
