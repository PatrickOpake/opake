<?php

namespace Opake\Model;

use DateInterval;
use Opake\Helper\Config;
use Opake\Helper\StringHelper;
use Opake\Helper\TimeFormat;
use OpakePatients\Exception\Authentication;
use RelationTest\Model\Role;

class User extends AbstractModel
{
	const VALIDATION_STAGE_BASIC_INFO = 'basic_info';
	const VALIDATION_STAGE_USERNAME_ONLY = 'username_only';
	const VALIDATION_STAGE_PASSWORD_ONLY = 'password_only';

	const STATUS_ACTIVE = 'active';
	const STATUS_INACTIVE = 'inactive';
	const DEFAULT_STATUS = self::STATUS_ACTIVE;
	const TYPE_INTERNAL = 'internal';
	const TYPE_EXTERNAL = 'external';
	const DEFAULT_TYPE = self::TYPE_EXTERNAL;

	const DEFAULT_CASE_COLOR = 'default-grey';

	const MIN_PASSWORD_LENGTH = 8;

	const ADDRESS_TYPE_HOME = 1;
	const ADDRESS_TYPE_OFFICE = 3;
	const ADDRESS_TYPE_OTHER = 4;
	const ADDRESS_TYPE_PO_BOX = 5;

	public $id_field = 'id';
	public $table = 'user';
	protected $_row = [
		'id' => null,
		'username' => null,
		'email' => null,
		'password' => null,
		'role_id' => null,
		'time_create' => null,
		'time_first_login' => null,
		'time_last_login' => null,
		'time_last_activity' => null,
		'first_name' => null,
		'last_name' => null,
		'status' => self::DEFAULT_STATUS,
		'address' => '',
		'phone' => '',
		'country_id' => null,
		'state_id' => null,
		'custom_state' => null,
		'city_id' => null,
		'custom_city' => null,
		'zip_code' => null,
		'timezone' => \Opake\Model\Timezone::DEFAULT_TIMEZONE,
		'type' => self::DEFAULT_TYPE,
		'profession_id' => null,
		'photo_id' => null,
		'time_status_change' => NULL,
		'organization_id' => '',
		'remember_me' => '',
		'hash' => '',
		'comment' => '',
		'practice_name' => null,
		'dea_number' => null,
		'dea_number_exp_date' => null,
		'medical_licence_number' => null,
		'medical_licence_number_exp_date' => null,
		'cds_number' => null,
		'cds_number_exp_date' => null,
		'case_color' => self::DEFAULT_CASE_COLOR,
		'is_temp_password' => 0,
		'is_messaging_active' => 1,
		'chat_last_readed_id' => null,
		'view_state' => '',
		'is_enabled_op_report' => null,
		'phone_type' => null,
		'address_type' => null,
		'last_password_change_date' => null,
		'is_scheduled_password_change' => 0,
		'is_dictation_enabled' => 0,
	];

	protected $belongs_to = [
		'role' => [
			'model' => 'Role',
			'key' => 'role_id'
		],
		'organization' => [
			'model' => 'Organization',
			'key' => 'organization_id'
		],
		'profession' => [
			'model' => 'Profession',
			'key' => 'profession_id'
		],
		'country' => [
			'model' => 'Geo_Country',
			'key' => 'country_id'
		],
		'city' => [
			'model' => 'Geo_City',
		    'key' => 'city_id'
		],
		'state' => [
			'model' => 'Geo_State',
		    'key' => 'state_id'
		],
		'time_zone' => [
			'model' => 'Timezone',
			'key' => 'timezone'
		],
		'photo' => [
			'model' => 'UploadedFile_Image',
			'key' => 'photo_id'
		]
	];

	protected $has_one = [
		'display_settings' => [
			'model' => 'User_DisplaySettings',
			'key' => 'user_id'
		],
		'credentials' => [
			'model' => 'User_Credentials',
			'key' => 'user_id'
		]
	];

	protected $has_many = array(
		'sites' => [
			'model' => 'Site',
			'through' => 'user_site',
			'key' => 'user_id',
			'foreign_key' => 'site_id'
		],
		'departments' => [
			'model' => 'Department',
			'through' => 'user_department',
			'key' => 'user_id',
			'foreign_key' => 'department_id'
		],
		'cards' => [
			'model' => 'PrefCard_Staff',
			'key' => 'user_id',
			'cascade_delete' => true
		],
		'practice_groups' => [
			'model' => 'PracticeGroup',
			'through' => 'user_practice_groups',
			'key' => 'user_id',
			'foreign_key' => 'practice_group_id'
		]
	);

	protected $formatters = [
		'SelectOptions' => [
			'class' => '\Opake\Formatter\User\SelectOptionsFormatter'
		],
	    'Form' => [
		    'class' => '\Opake\Formatter\User\FormDataFormatter'
	    ]
	];

	/**
	 * @param mixed $stage
	 * @return \PHPixie\Validate\Validator
	 */
	public function getValidator($stage = null)
	{
		/* @var $validator \Opake\Extentions\Validate */
		$validator = parent::getValidator();

		$validator->field('username')->rule('filled')->error('Username is empty');
		$validator->field('username')->rule('matches', '/^([A-z0-9_\-\']*)([\.]?)([A-z0-9_\-\']*)$/')
			->error('Username contains incorrect symbols');
		$validator->field('username')->rule('min_length', 4)
			->error('Minimum username length is 4 symbols');
		$validator->field('username')->rule('callback', function ($val, $validator, $field) {
			$model = $this->pixie->orm->get('User')->where('username', $this->username)->find();
			return !($model->loaded() && $this->id() != $model->id);
		})->error(sprintf('Username is already taken!', $this->username));

		if ($stage === self::VALIDATION_STAGE_USERNAME_ONLY) {
			return $validator;
		}

		if ($this->isSatelliteOffice()) {
			$validator->field('practice_groups')->rule('filled')->error('You must select at least one practice group');
		}

		$validator->field('first_name')->rule('filled')->rule('min_length', 2)->error('Invalid First Name');
		$validator->field('email')->rule('filled')->rule('email')->error('Invalid Email');
		$validator->field('phone')->rule('phone')->error('Invalid Phone');
		if ($stage !== self::VALIDATION_STAGE_BASIC_INFO) {
			$validator->field('sites')->rule('filled')->error('You must select at least one site');
			$validator->field('departments')->rule('filled')->error('You must select at least one department');
		}

		$validator->field('email')->rule('callback', function ($val, $validator, $field) {
			$model = $this->pixie->orm->get('User')->where('email', $this->email)->find();
			return !($model->loaded() && $this->id() != $model->id);
		})->error(sprintf('User with email %s already exists', $this->email));

		$validator->field('role_id')->rule('filled')->error('Invalid Role');
		$validator->field('profession_id')->rule('filled')->error('Invalid Type');

		return $validator;
	}

	public function getPasswordValidator($newPassword, $confirmPassword)
	{
		$validator = $this->pixie->validate->get([
			'new_password' => $newPassword,
			'confirm_password' => $confirmPassword
		]);

		$validator->field('new_password')->rule('filled')->error('Password is empty');

		$validator->field('new_password')->rule('min_length', 8)->error('Minimal password length is 8 symbols');
		$validator->field('new_password')->rule('callback', function($password) {

			$uppercase = preg_match('/[A-Z]/', $password);
			$lowercase = preg_match('/[a-z]/', $password);
			$special   = preg_match("/[" . preg_quote('~`!@#$%^&*()_+{}[]:;"\'|?/>.<,-=', '/') . "]/", $password);
			$len = StringHelper::strlen($password);

			return ($uppercase && $lowercase && $special && $len >= \Opake\Model\User::MIN_PASSWORD_LENGTH);

		})->error('Passwords must follow requirements above');

		$validator->field('new_password')->rule('callback', function() use ($confirmPassword) {
			return (bool) $confirmPassword;
		})->error('Password confirm is empty');
		$validator->field('new_password')->rule('same_as', 'confirm_password')->error('Passwords do not match!');

		$lastPasswordsCount = (int) $this->pixie->config->get('app.password_change_reminder.last_passwords_count');
		$validator->field('new_password')->rule('callback', function($password) {
			if (!$this->loaded()) {
				return true;
			}

			$service = $this->pixie->services->get('User');
			return $service->checkPasswordNotUsed($this, $password);

		})->error("You can't repeat the last " . $lastPasswordsCount . " passwords. Please enter a new one.");

		return $validator;
	}

	public function isSelf($user)
	{
		if (isset($this->id)) {
			return $this->id == $user->id;
		}
		return false;
	}

	public function updateMessagingActive($active)
	{
		$this->conn->query('update')->table($this->table)
			->data(['is_messaging_active' => $active])
			->where('id', $this->id)
			->execute();
		$this->is_messaging_active = $active;
	}

	public function updateChatLastReaded($last_readed_id)
	{
		if ($last_readed_id > $this->chat_last_readed_id) {
			$this->conn->query('update')->table($this->table)
				->data(['chat_last_readed_id' => $last_readed_id])
				->where('id', $this->id)
				->execute();
			$this->chat_last_readed_id = $last_readed_id;
		}
	}

	public function getFirstName()
	{
		return $this->first_name;
	}

	public function getLastName()
	{
		return $this->last_name;
	}

	public function getFullName()
	{
		return trim(sprintf('%s %s', $this->first_name, $this->last_name));
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getDefaultSite()
	{
		if (!isset($this->defaultSite)) {
			$this->defaultSite = $this->sites->find();
		}
		return $this->defaultSite;
	}

	public function getHash()
	{
		return $this->hash;
	}

	public function getPhoto($size = NULL)
	{
		if ($photo = $this->getPhotoModel()) {
			return $photo->getThumbnailWebPath($size);
		}

		if ($size) {
			return '/i/user_profile_' . $size . '.png';
		}

		return '/i/user_profile.png';
	}

	public function getSites()
	{
		return $this->sites->where('active', true)->find_all()->as_array();
	}

	public function getDepartments()
	{
		return $this->departments->find_all()->as_array();
	}

	public function getCaseColor()
	{
		return $this->case_color ? $this->case_color : self::DEFAULT_CASE_COLOR;
	}

	public function setLastLoginDate($time = NULL)
	{
		if (!$time) {
			$time = time();
		}
		$this->time_last_login = strftime('%Y-%m-%d %H:%M:%S', $time);
	}

	public function setFirstLoginDate($time = NULL)
	{
		if (!$time) {
			$time = time();
		}
		$this->time_first_login = strftime('%Y-%m-%d %H:%M:%S', $time);
	}

	public function setLastVisitedPage($uri)
	{
		$this->pixie->cache->set('last_visited_user_'.$this->id(), $uri);
	}

	public function getLastVisitedPage()
	{
		return $this->pixie->cache->get('last_visited_user_'.$this->id());
	}

	public function setHash($str = '')
	{
		if (!$str) {
			$str = md5(microtime());
		}
		$this->hash = $str;
	}

	public function setStatus($status)
	{
		if (!in_array($status, array(self::STATUS_ACTIVE, self::STATUS_INACTIVE))) {
			throw new \Exception('Invalid status');
		}
		if ($this->status != $status) {
			$this->time_status_change = strftime('%Y-%m-%d %H:%M:%S');
			$this->status = $status;
		}
	}

	public function setPassword($password)
	{
		$this->password = $this->pixie->auth->provider('password')->hash_password($password);
	}

	/**
	 * Возвращает true, если пользователь внутренний
	 * @return boolean
	 */
	public function isInternal()
	{
		return $this->type == self::TYPE_INTERNAL;
	}

	public function isDoctor()
	{
		return \Opake\Model\Role::Doctor == $this->role_id;
	}

	public function isDictation()
	{
		return \Opake\Model\Role::Dictation == $this->role_id;
	}

	public function isSatelliteOffice()
	{
		return \Opake\Model\Role::SatelliteOffice == $this->role_id;
	}

	public function isFullAdmin()
	{
		return \Opake\Model\Role::FullAdmin == $this->role_id;
	}

	public function isBiller()
	{
		return \Opake\Model\Role::Biller == $this->role_id;
	}

	public function isFullClinical()
	{
		return \Opake\Model\Role::FullClinical == $this->role_id;
	}

	public function isAnesthesiologist()
	{
		return Profession::ANESTHESIOLOGIST == $this->profession_id;
	}

	public function save()
	{
		if ($this->time_create == NULL) {
			$this->time_create = strftime('%Y-%m-%d %H:%M:%S');
		}
		if ($this->time_status_change == NULL) {
			$this->time_status_change = $this->time_create;
		}
		if (!$this->status) {
			$this->status = self::DEFAULT_STATUS;
		}
		if ($this->isInternal()) {
			$this->status = self::STATUS_ACTIVE;
		}

		parent::save();
	}

	public function fromArray($data)
	{
		unset($data->view_state);

		return $data;
	}

	public function getReportCount()
	{
		$model = $this->pixie->orm->get('Cases_OperativeReport');
		$search = new \OpakeAdmin\Model\Search\Cases\OperativeReport($this->pixie);
		return $search->getCountByAlert($model, 'open', $this->id);
	}

	public function getSubmittedReportCount()
	{
		$model = $this->pixie->orm->get('Cases_OperativeReport');
		$search = new \OpakeAdmin\Model\Search\Cases\OperativeReport($this->pixie);

		return $search->getCountByAlert($model, 'submitted', $this->id);
	}

	public function getReportTemplateCount()
	{
		$model = $this->pixie->orm->get('Cases_OperativeReport_Future');
		$search = new \OpakeAdmin\Model\Search\Cases\OperativeReport\Future($this->pixie);
		return $search->getCountByUser($model, $this->id);
	}

	public function hasExpiredCredentials()
	{
		if($this->credentials->loaded()) {
			return $this->pixie->orm->get('User_Credentials_Alert')->where(
				['credentials_id', $this->credentials->id()],
				['status', \Opake\Model\User\Credentials\Alert::STATUS_ACTIVE]
			)->count_all();
		}
		return false;
	}

	public function updateLastActivity()
	{
		$time = time();
		$time_last_activity = strftime('%Y-%m-%d %H:%M:%S', $time);

		$this->conn->query('update')->table($this->table)
			->data(['time_last_activity' => $time_last_activity])
			->where('id', $this->id)
			->execute();
	}

	public function isOnline()
	{
		if(!$this->time_last_activity) {
			return false;
		}
		$currentTime =  (new \DateTime())->setTimestamp(time());
		$timeLastActivity = (new \DateTime())->setTimestamp(strtotime($this->time_last_activity))->add(new DateInterval('PT1M'));
		return $currentTime <= $timeLastActivity;
	}

	public function toArray()
	{
		$sites = [];
		$departments = [];

		$siteIds = [];
		$departmentIds = [];

		foreach ($this->getSites() as $site) {
			$sites[] = $site->toArray();
			$siteIds[] = (int)$site->id();
		}

		foreach ($this->getDepartments() as $department) {
			$departments[] = $department->toArray();
			$departmentIds[] = (int)$department->id();
		}

		$practiceGroups = [];
		$practiceGroupIds = [];
		foreach ($this->practice_groups->find_all() as $practiceGroup) {
			$practiceGroupIds[] = (int) $practiceGroup->id();
			$practiceGroups[] = $practiceGroup->toArray();
		}

		return [
			'id' => (int)$this->id,
			'image' => $this->getPhoto('tiny'),
			'image_default' => $this->getPhoto('default'),
			'fullname' => $this->getFullName(), // TODO: Надо будет впилить
			'full_name' => $this->getFullName(),
			'first_name' => $this->getFirstName(),
			'last_name' => $this->getLastName(),
			'username' => $this->username,
			'email' => $this->email,
			'phone' => $this->phone,
			'case_color' => $this->getCaseColor(),
			'is_temp_password' => $this->is_temp_password == 1 ? true : false,
			'is_scheduled_password_change' =>  $this->is_scheduled_password_change == 1 ? true : false,
			'is_internal' => $this->isInternal(),
			'last_change' => TimeFormat::getDateTime($this->time_status_change),
			'country' => ($this->country && $this->country->loaded()) ? $this->country->toArray() : null,
			'country_id' => $this->country_id,
			'address' => $this->address,
			'sites' => $sites,
			'departments' => $departments,
			'profession' => ($this->profession && $this->profession->loaded()) ? $this->profession->toArray() : null,
			'user_access' => ($this->role && $this->role->loaded()) ? $this->role->toArray() : null,
			'comment' => $this->comment,
			'practice_name' => $this->practice_name,
			'dea_number' => $this->dea_number,
			'dea_number_exp_date' => $this->dea_number_exp_date,
			'medical_licence_number' => $this->medical_licence_number,
			'medical_licence_number_exp_date' => $this->medical_licence_number_exp_date,
			'cds_number' => $this->cds_number,
			'cds_number_exp_date' => $this->cds_number_exp_date,
			'status' => $this->getStatus(),
			'photo_id' => $this->photo_id,
			'role_id' => $this->role_id,
			'profession_id' => $this->profession_id,
			'site_ids' => $siteIds,
			'department_ids' => $departmentIds,
			'is_messaging_active' => (bool)$this->is_messaging_active,
			'chat_last_readed_id' => (int)$this->chat_last_readed_id,
			'is_enabled_op_report' => (bool)$this->is_enabled_op_report,
			'is_dictation_enabled' => (bool)$this->is_dictation_enabled,
			'overview_display_position' => (int)$this->display_settings->overview_position,
			'phone_type' => $this->phone_type,
			'address_type' => $this->address_type,
			'practice_groups' => $practiceGroups,
			'organization_id' => $this->organization_id
		];
	}

	public function toShortArray()
	{
		$sites = [];
		foreach ($this->getSites() as $site) {
			$sites[] = $site->toShortArray();
		}

		return [
			'id' => (int)$this->id,
			'image' => $this->getPhoto('tiny'),
			'username' => $this->username,
			'full_name' => $this->getFullName(),
			'email' => $this->email,
			'sites' => $sites,
			'status' => $this->status,
			'time_first_login' => $this->time_first_login,
			'time_last_login' => $this->time_last_login,
			'is_enabled_op_report' => (bool)$this->is_enabled_op_report,
			'overview_display_position' => (int)$this->display_settings->overview_position,
			'case_color' => $this->getCaseColor()
		];
	}

	public function toScheduleLegendArray()
	{
		return [
			'id' => (int)$this->id,
			'full_name' => $this->getFullName(),
			'case_color' => $this->getCaseColor()
		];
	}

	public function toBlockingArray()
	{
		return [
			'id' => (int)$this->id,
			'fullname' => $this->getFullName(),
			'email' => $this->email,
			'case_color' => $this->getCaseColor()
		];
	}

	public function toDashboardArray()
	{
		return [
			'id' => (int)$this->id,
			'full_name' => $this->getFullName(),
			'overview_display_position' => (int)$this->display_settings->overview_position
		];
	}

	public function updateDepartments($departmentIds)
	{
		$this->departments->delete_all();
	}

	public function updateSites($siteIds)
	{
		$this->sites->delete_all();
	}

	public function getPracticeGroupIds()
	{
		$ids = [];
		$rows = $this->pixie->db->query('select')
			->table('user_practice_groups')
			->fields('practice_group_id')
			->join('practice_groups', ['user_practice_groups.practice_group_id', 'practice_groups.id'])
			->where('user_id', $this->id())
			->where('practice_groups.active', 1)
			->execute();

		foreach ($rows as $row) {
			$ids[] = (int) $row->practice_group_id;
		}

		return $ids;
	}

	public function getFirstPracticeGroupName()
	{
		$query = $this->pixie->db->query('select')
			->table('practice_groups')
			->fields('name')
			->join('user_practice_groups', ['user_practice_groups.practice_group_id', 'practice_groups.id'])
			->where('user_practice_groups.user_id', $this->id())
			->where('active', 1)
			->limit(1)
			->execute()
			->current();

		if ($query && $query->name) {
			return $query->name;
		}

		return '';
	}

	/**
	 * @return \Opake\Model\UploadedFile\Image
	 */
	public function getPhotoModel()
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

	public function getViewState($useCache = true)
	{
		if ($useCache) {
			$viewState = $this->view_state;
		} else {
			$row = $this->pixie->db->query('select')
				->table('user')
				->fields('view_state')
				->where('id', $this->id)
				->execute()->current();
			$viewState = $row ? $row->view_state : '';
		}

		$viewState = json_decode($viewState, true);
		if (!is_array($viewState)) {
			$viewState = [];
		}
		return $viewState;
	}

	public function getViewStateByKey($key, $useCache = true)
	{
		$viewState = $this->getViewState($useCache);
		if (isset($viewState[$key])) {
			return $viewState[$key];
		}
		return null;
	}

	public function updateLoginTime()
	{
		if ($this->loaded()) {
			$now = new \DateTime();
			$update = [];
			if (!$this->time_first_login) {
				$update['time_first_login'] = TimeFormat::formatToDBDatetime($now);
			}
			if (!$this->time_last_login) {
				$update['time_last_login'] = TimeFormat::formatToDBDatetime($now);
			} else {
				$lastLoginTime = TimeFormat::fromDBDatetime($this->time_last_login);
				if (($now->getTimestamp() - $lastLoginTime->getTimestamp()) >= 60) {
					$update['time_last_login'] = TimeFormat::formatToDBDatetime($now);
				}
			}

			if ($update) {
				$this->pixie->db->query('update')
					->table($this->table)
					->data($update)
					->where('id', $this->id())
					->execute();
			}
		}
	}

	public function updateViewState($key, $value)
	{
		$viewState = $this->getViewState();
		$viewState[$key] = $value;
		$viewStateStr = json_encode($viewState);
		if (!$viewStateStr) {
			throw new \Exception('Incorrect view state');
		}
		if (strlen($viewStateStr) > 1000) {
			throw new \Exception('Too much data for view state');
		}
		$this->view_state = $viewStateStr;
		$this->pixie->db->query('update')
			->table('user')
			->data(['view_state' => $viewStateStr])
			->where('id', $this->id)
			->execute();
	}

	public function updateOverviewPosition($overviewPosition)
	{
		if ($this->display_settings && $this->display_settings->overview_position) {
			$this->pixie->db->query('update')
				->table('surgeon_display_settings')
				->data(['overview_position' => $overviewPosition])
				->where('user_id', $this->id)
				->execute();
		} else {
			$this->pixie->db->query('insert')
				->table('surgeon_display_settings')
				->data(['user_id' => $this->id, 'overview_position' => $overviewPosition])
				->execute();
		}
	}
}
