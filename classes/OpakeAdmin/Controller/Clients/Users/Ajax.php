<?php

namespace OpakeAdmin\Controller\Clients\Users;

use Opake\ActivityLogger\ModelActionQueue;
use Opake\Exception\Forbidden;
use Opake\Exception\InvalidMethod;
use Opake\Exception\PageNotFound;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Role;
use OpakeAdmin\Model\Search\User as UserSearch;
use Opake\Model\User;
use Opake\Model\Profession;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function before()
	{
		parent::before();

		$this->iniOrganization($this->request->param('id'));
	}

	public function actionIndex()
	{
		$items = [];
		$model = $this->orm->get('User')->where('organization_id', $this->org->id);

		$search = new UserSearch($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->toShortArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionUser()
	{
		$model = $this->loadModel('User', 'subid');
		$this->result = $model->getFormatter('Form')->toArray();
	}

	public function actionAllowedPracticeGroups()
	{
		$model = $this->loadModel('User', 'subid');
		if (!$model->loaded()) {
			throw new PageNotFound();
		}

		$loggedUser = $this->logged();
		if (!$loggedUser || (!$loggedUser->isInternal() && $loggedUser->organization_id != $model->organization_id)) {
			throw new Forbidden();
		}

		$organization = $model->organization;
		if (!$organization->loaded()) {
			throw new PageNotFound();
		}

		$practiceGroups = [];
		$results = $organization->practice_groups->where('active', 1)
			->find_all();
		foreach ($results as $group) {
			$practiceGroups[] = $group->toArray();
		}

		$this->result = $practiceGroups;
	}

	public function actionUsers()
	{
		$users = $this->org->users;

		if ($this->request->get('profession')) {
			$profession = json_decode($this->request->get('profession'));
			if(is_array($profession)) {
				$users->where('profession_id', 'IN', $this->pixie->db->expr('(' . implode(', ', $profession) . ')'));

			} else {
				$users->where('profession_id', $profession);
			}

			if ($this->request->get('role')) {
				$role = $this->request->get('role');
				$users->where(['or', ['role_id', $role]]);
			}
			$users->where('organization_id', $this->org->id);
		}

		if ($this->request->get('role')) {
			$role = $this->request->get('role');
			$users->where('role_id', $role);
		}

		$users->where('status', \Opake\Model\User::STATUS_ACTIVE);

		$currentUser = $this->pixie->auth->user();
		if ($currentUser->isSatelliteOffice()) {

			$userPracticeGroupIds = $currentUser->getPracticeGroupIds();
			if ($userPracticeGroupIds) {
				$users->query
					->join(['user_practice_groups', 'upg'], ['id', 'upg.user_id'], 'left');

				$users->where('upg.practice_group_id', 'IN', $this->pixie->db->expr("(" . implode(',', $userPracticeGroupIds) . ")"));
				$users->query->group_by('id');
			}
		}

		$results = [];
		foreach ($users->find_all() as $user) {
			$results[] = $user->toArray();
		}
		$this->result = $results;
	}

	public function actionNonSurgicalUsers()
	{
		$medicalProfessionsIds = [Profession::SURGEON, Profession::ANESTHESIOLOGIST, Profession::PHYSICIAN_ASSISTANT,
			Profession::NURSE_ANESTHETIST, Profession::NURSE_PRACTITIONER];

		$users = $this->org->users;
		$users->where('profession_id', 'NOT IN', $this->pixie->db->expr('(' . implode(', ', $medicalProfessionsIds) . ')'));
		$users->where('status', \Opake\Model\User::STATUS_ACTIVE);

		$results = [];
		foreach ($users->find_all() as $user) {
			$results[] = $user->toArray();
		}

		$this->result = $results;
	}

	public function actionSave()
	{
		if ($this->request->method !== 'POST') {
			throw new InvalidMethod('Method not allowed');
		}

		$data = $this->getData();
		$user = $this->pixie->orm->get('User', (isset($data->id)) ? $data->id : null);

		if (isset($data->id) && !$user->loaded()) {
			throw new PageNotFound('Unknown user');
		}

		if (!$user->loaded()) {
			if (!$this->pixie->permissions->checkAccess('user', 'create')) {
				throw new Forbidden();
			}
		} else {
			if (!$this->pixie->permissions->checkAccess('user', 'edit', $user)) {
				throw new Forbidden();
			}
		}

		$filterValues = [
			'sites',
			'departments',
			'profession',
			'user_access',
			'image',
			'image_default'
		];

		foreach ($filterValues as $key) {
			if (isset($data->$key)) {
				unset($data->$key);
			}
		}

		$data->country_id = null;
		if (!empty($data->country)) {
			$data->country_id = $data->country->id;
		}
		$data->city_id = null;
		if (!empty($data->city)) {
			$data->city_id = $data->city->id;
		}
		$data->state_id = null;
		if (!empty($data->state)) {
			$data->state_id = $data->state->id;
		}

		if (isset($data->is_active)) {
			$data->status = ($data->is_active) ? User::STATUS_ACTIVE : User::STATUS_INACTIVE;
			unset($data->is_active);
		}

		if (isset($data->department_ids)) {
			$data->departments = $data->department_ids;
		}

		if (isset($data->site_ids)) {
			$data->sites = $data->site_ids;
		}

		if (isset($data->practice_groups)) {
			$practiceGroupIds = [];
			foreach ($data->practice_groups as $practiceGroup) {
				$practiceGroupIds[] = $practiceGroup->id;
			}
			$data->practice_groups = $practiceGroupIds;
		}

		$newPassword = null;
		$confirmNewPassword = null;

		if (isset($data->new_password)) {
			$newPassword = $data->new_password;
		}
		if (isset($data->confirm_new_password)) {
			$confirmNewPassword = $data->confirm_new_password;
		}

		$user->fill($data);

		if (!$user->loaded()) {
			$user->organization_id = $this->org->id;
		}

		$validationStage = null;
		if (!$this->pixie->permissions->checkAccess('user', 'edit_not_basic', $user)) {
			$validationStage = \Opake\Model\User::VALIDATION_STAGE_BASIC_INFO;
		}

		$validator = $user->getValidator($validationStage);

		$formErrors = [];
		if ($validator->valid()) {
			try {

				if ($newPassword) {
					$this->setUserPassword($user, $newPassword, $confirmNewPassword);
				}

				$actionQueue = $this->createUserActionQueue($user);

				$user->save();

				if ($newPassword) {
					$service = $this->pixie->services->get('User');
					$service->updateUsedPasswords($user, $newPassword);
				}

				$this->updateCredentials($user);

				$actionQueue->registerActions();

			} catch (\Exception $e) {
				$this->logSystemError($e);
				$formErrors = [$e->getMessage()];
			}

		} else {
			$formErrors = [];
			foreach ($validator->errors() as $field => $errors) {
				$formErrors[] = implode(', ', $errors);
			}
		}

		if ($formErrors) {
			$this->result = ['errors' => $formErrors];
		} else {
			$this->result = ['id' => (int) $user->id()];
		}
	}

	public function actionSendPasswordEmail()
	{
		if ($this->request->method !== 'POST') {
			throw new InvalidMethod('Method not allowed');
		}

		$user = $this->pixie->orm->get('User', $this->request->param('subid') ? : null);

		if (!$user->loaded()) {
			throw new PageNotFound('Unknown user');
		}

		$user->setHash();
		$user->is_temp_password = false;
		$user->save();
		$mailer = new \Opake\Helper\Mailer();
		$mailer->sendPwdEmail($user);

		$this->pixie->activityLogger->newAction(ActivityRecord::ACTION_SETTINGS_SEND_PW_EMAIL)
			->setModel($user)
			->register();

		$this->result = ['success' => true];

	}

	public function actionCreatePasswordByAdmin()
	{
		$this->checkAccess('user', 'edit');

		if ($this->request->method !== 'POST') {
			throw new \Opake\Exception\InvalidMethod();
		}

		$userId = $this->request->post('user_id');
		$newPassword = $this->request->post('password');
		/** @var \Opake\Extentions\Validate $validator */
		$validator =  $this->pixie->validate->get([
			'password' => $newPassword
		]);
		$validator->field('password')
			->rule('filled')
			->rule('min_length', 8)->error('Min length is 8 symbols');

		if ($validator->valid()) {

			/** @var \Opake\Model\User $user */
			$user = $this->orm->get('User', $userId);
			if (!$user->loaded()) {
				throw new \Opake\Exception\PageNotFound('Unknown user');
			}

			$user->setPassword($newPassword);
			$loggedUser = $this->logged();

			if ($loggedUser->id() == $user->id()) {
				$user->is_temp_password = 0;
			} else {
				$user->is_temp_password = 1;
			}

			$user->save();

		} else {
			throw new \Opake\Exception\BadRequest('Invalid password: ' . $this->getFirstError($validator));
		}

		$this->result = [
			'result' => 'success'
		];
	}

	public function actionValidateUsername()
	{
		if ($this->request->method !== 'POST') {
			throw new InvalidMethod('Method not allowed');
		}

		$user = $this->pixie->orm->get('User', $this->request->param('subid') ? : null);
		$user->username = $this->request->post('username', null, false);

		$validator = $user->getValidator(User::VALIDATION_STAGE_USERNAME_ONLY);

		if (!$validator->valid()) {

			$errors = $validator->errors();
			$errorMessage = '';
			if (isset($errors['username'])) {
				$usernameErrors = $errors['username'];
				$errorMessage = reset($usernameErrors);
			}

			$this->result = [
				'success' => false,
				'error' => $errorMessage
			];
		} else {
			$this->result = [
				'success' => true
			];
		}
	}

	public function actionValidatePassword()
	{
		if ($this->request->method !== 'POST') {
			throw new InvalidMethod('Method not allowed');
		}

		$newPassword = $this->request->post('new_password', null, false);
		$confirmPassword = $this->request->post('confirm_new_password', null, false);

		$user = $this->pixie->orm->get('User', $this->request->param('subid'));
		$passwordValidator = $user->getPasswordValidator($newPassword, $confirmPassword);

		if (!$passwordValidator->valid()) {

			$errors = $passwordValidator->errors();
			$errorMessage = '';
			if (isset($errors['new_password'])) {
				$usernameErrors = $errors['new_password'];
				$errorMessage = reset($usernameErrors);
			}

			$this->result = [
				'success' => false,
				'error' => $errorMessage
			];

		} else {
			$this->result = [
				'success' => true
			];
		}
	}

	public function actionUsersAndPractices()
	{
		$users = $this->org->users;

		$users->where('role_id', Role::Doctor)
			->where('status', \Opake\Model\User::STATUS_ACTIVE);

		$currentUser = $this->logged();
		if ($currentUser->isSatelliteOffice()) {
			$userPracticeGroupIds = $currentUser->getPracticeGroupIds();
			if ($userPracticeGroupIds) {
				$users->query->join(['user_practice_groups', 'upg'], ['id', 'upg.user_id'], 'left');
				$users->where('upg.practice_group_id', 'IN', $this->pixie->db->expr("(" . implode(',', $userPracticeGroupIds) . ")"));
			}
		}

		$usersArray = [];
		foreach ($users->find_all() as $user) {
			$usersArray[] = $user->toArray();
		}

		$organization  = $this->org;
		$practiceGroups = $organization->practice_groups->where('active', 1)
			->find_all();

		$practicesArray = [];
		foreach ($practiceGroups as $group) {
			$practicesArray[] = $group->toExpandedArray($organization->id);
		}

		$this->result = array_merge($usersArray, $practicesArray);
	}

	public function actionUpdateViewState()
	{
		$user = $this->logged();
		$data = $this->getData();
		$key = $this->request->post('key');

		// TODO: we need some manager here
		if ($key === 'cases_view_date') {
			try {
				$data = \Opake\Helper\TimeFormat::formatToDB($data);
			} catch (\Exception $e) {
				throw new \Opake\Exception\Ajax('Wrong date format');
			}
		}
		try {
			$user->updateViewState($key, $data);
		} catch (\Exception $e) {
			$this->logSystemError($e);
			throw new \Opake\Exception\BadRequest('Invalid parameters');
		}
	}

	protected function getFirstError($validator)
	{
		$errors = $validator->errors();
		if ($errors) {
			$fieldErrors = reset($errors);
			if ($fieldErrors) {
				return reset($fieldErrors);
			}
		}

		return null;
	}

	protected function setUserPassword($user, $password, $confirmPassword)
	{
		$passwordValidator = $user->getPasswordValidator($password, $confirmPassword);

		if (!$passwordValidator->valid()) {
			$errors = $passwordValidator->errors();
			if (isset($errors['new_password'])) {
				$usernameErrors = $errors['new_password'];
				$errorMessage = reset($usernameErrors);
				throw new \Exception($errorMessage);
			}
		}

		$service = $this->pixie->services->get('User');
		$service->setNewPassword($user, $password);

		if ($this->logged()->id() != $user->id()) {
			$user->is_temp_password = 1;
		}
	}

	/**
	 * @param User $user
	 * @return ModelActionQueue
	 */
	protected function createUserActionQueue($user)
	{
		/** @var \Opake\ActivityLogger $logger */
		$logger = $this->pixie->activityLogger;

		$queue = $logger->newModelActionQueue($user);

		if (!$user->loaded()) {
			$queue->addAction(ActivityRecord::ACTION_SETTINGS_CREATE_USER);
		} else {
			if ($user->id() == $this->logged()->id()) {
				$queue->addAction(ActivityRecord::ACTION_EDIT_PROFILE);
				$queue->addAction(ActivityRecord::ACTION_RESET_PW);
				$queue->addAction(ActivityRecord::ACTION_EDIT_PERMISSIONS);
			} else {
				$queue->addAction(ActivityRecord::ACTION_SETTINGS_RESET_PW);
				$queue->addAction(ActivityRecord::ACTION_SETTINGS_EDIT_USERS);
				$queue->addAction(ActivityRecord::ACTION_SETTINGS_EDIT_PERMISSIONS);
			}
		}

		$queue->assign();

		return $queue;
	}

	protected function updateCredentials($user)
	{
		$model = $this->pixie->orm->get('User_Credentials')
			->where('user_id', $user->id())
			->find();

		if (!$model->loaded()) {
			$model = $this->pixie->orm->get('User_Credentials');
			$model->user_id = $user->id();
		}

		$model->save();
	}

}