<?php

namespace OpakeAdmin\Controller\Clients\Profile;

use Opake\Exception\Forbidden;
use Opake\Exception\InvalidMethod;
use Opake\Exception\PageNotFound;
use Opake\Model\Analytics\UserActivity\ActivityRecord;
use Opake\Model\Organization;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function actionOrganizationProfile()
	{
		$model = $this->loadModel('Organization', 'id');
		$this->result = $model->toArray();
	}

	public function actionDefaultSitePermissions()
	{
		$orgPermissions = new \Opake\Permissions\Organization\OrganizationLevel();
		$this->result = [
			'settings' => $orgPermissions->getDefaultPermissions(),
			'hierarchy' => $orgPermissions->getPermissionsHierarchy()
		];
	}

	public function actionAllPracticeGroups()
	{
		$results = $this->orm->get('PracticeGroup')
			->where('active', 1)->find_all();

		$practiceGroups = [];

		foreach ($results as $group) {
			$practiceGroups[] = $group->toArray();
		}

		$this->result = $practiceGroups;
	}

	public function actionAllowedPracticeGroups()
	{
		$model = $this->loadModel('Organization', 'id');
		if (!$model->loaded()) {
			throw new PageNotFound();
		}

		$loggedUser = $this->logged();
		if (!$loggedUser || (!$loggedUser->isInternal() && $loggedUser->organization_id != $model->id())) {
			throw new Forbidden();
		}

		$practiceGroups = [];
		$results = $model->practice_groups->where('active', 1)
			->find_all();

		foreach ($results as $group) {
			$practiceGroups[] = $group->toArray();
		}

		$this->result = $practiceGroups;
	}

	public function actionAllowedPracticeGroupsWithColors()
	{
		$model = $this->loadModel('Organization', 'id');
		if (!$model->loaded()) {
			throw new PageNotFound();
		}

		$loggedUser = $this->logged();
		if (!$loggedUser || (!$loggedUser->isInternal() && $loggedUser->organization_id != $model->id())) {
			throw new Forbidden();
		}

		$practiceGroups = [];
		$results = $model->practice_groups->where('active', 1)->order_by('name', 'asc')
			->find_all();

		foreach ($results as $group) {
			$practiceGroups[] = $group->toExpandedArray($model->id());
		}

		$this->result = $practiceGroups;
	}

	public function actionGetPrefCardStages()
	{
		$items = [];
		$stages = $this->orm->get('PrefCard_Stage')->where('is_requested_items', false)->find_all();
		foreach ($stages as $result) {
			$items[] = $result->toArray();
		}

		$this->result = $items;
	}

	public function actionGetAllPrefCardStages()
	{
		$items = [];
		$stages = $this->orm->get('PrefCard_Stage')->find_all();
		foreach ($stages as $result) {
			$items[] = $result->toArray();
		}

		$this->result = $items;
	}

	public function actionSave()
	{
		if ($this->request->method !== 'POST') {
			throw new InvalidMethod('Method not allowed');
		}

		$data = $this->getData();

		$org = $this->pixie->orm->get('Organization', (isset($data->id)) ? $data->id : null);

		if (isset($data->id) && !$org->loaded()) {
			throw new PageNotFound('Unknown organization');
		}

		if (!$org->loaded()) {
			if (!$this->pixie->permissions->checkAccess('organization', 'create')) {
				throw new Forbidden();
			}
		} else {
			if (!$this->pixie->permissions->checkAccess('organization', 'edit', $org)) {
				throw new Forbidden();
			}
		}

		$filterValues = [
			'country',
			'time_create'
		];

		foreach ($filterValues as $key) {
			if (isset($data->$key)) {
				unset($data->$key);
			}
		}

		if (isset($data->is_active)) {
			$data->status = ($data->is_active) ? Organization::STATUS_ACTIVE : Organization::STATUS_INACTIVE;
			unset($data->is_active);
		}

		if (isset($data->practice_groups)) {
			$practiceGroupIds = [];
			foreach ($data->practice_groups as $practiceGroup) {
				$practiceGroupIds[] = $practiceGroup->id;
			}
			$data->practice_groups = $practiceGroupIds;
		}

		$org->fill($data);
		$validator = $org->getValidator();

		$formErrors = [];
		if ($validator->valid()) {

			$queue = $this->pixie->activityLogger->newModelActionQueue($org);
			if ($org->loaded()) {
				$queue->addAction(ActivityRecord::ACTION_SETTINGS_EDIT_ORGANIZATION);
			}
			$queue->assign();

			$org->save();

			if (isset($data->permissions->settings)) {
				$org->updatePermissions($data->permissions->settings);
			}

			$this->updateUserPracticeGroups($org->id());

			$queue->registerActions();

		} else {
			$formErrors = [];
			foreach ($validator->errors() as $field => $errors) {
				$formErrors[] = implode(', ', $errors);
			}
		}

		if ($formErrors) {
			$this->result = ['errors' => $formErrors];
		} else {
			$this->result = ['id' => (int) $org->id()];
		}
	}

	public function actionGetChargeable()
	{
		$model = $this->loadModel('Organization', 'id');

		$this->result = [
			'charge_price' => number_format($model->chargeable, 2, '.', '')
		];
	}

	public function actionSaveChargeable()
	{
		$model = $this->loadModel('Organization', 'id');
		$data = $this->getData();
		$model->updateChargeable($data->charge_price);

		$this->result = ['id' => (int) $model->id()];
	}

	protected function updateUserPracticeGroups($organizationId)
	{
		$rows = $this->pixie->db->query('select')
			->table('organization_practice_groups')
			->fields('practice_group_id')
			->where('organization_id', $organizationId)
			->execute();

		$usedGroupIds = [];
		foreach ($rows as $row) {
			$usedGroupIds[] = (int) $row->practice_group_id;
		}

		$query = $this->pixie->db->query('select')
			->table('user_practice_groups')
			->fields('user_practice_groups.user_id', 'user_practice_groups.practice_group_id')
			->join('user', ['user_practice_groups.user_id', 'user.id'])
			->where('user.organization_id', $organizationId);

		if ($usedGroupIds) {
			$query->where('practice_group_id', 'NOT IN', $this->pixie->db->expr('(' . implode(', ', $usedGroupIds) . ')'));
		}

		$this->pixie->db->begin_transaction();
		try {
			foreach ($query->execute() as $row) {
				$this->pixie->db->query('delete')
					->table('user_practice_groups')
					->where('user_practice_groups.user_id', $row->user_id)
					->where('user_practice_groups.practice_group_id', $row->practice_group_id)
					->execute();
			}

			$this->pixie->db->commit();
		} catch (\Exception $e) {
			$this->pixie->db->rollback();
			throw $e;
		}
	}

}