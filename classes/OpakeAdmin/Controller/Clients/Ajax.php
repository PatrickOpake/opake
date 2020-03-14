<?php

namespace OpakeAdmin\Controller\Clients;

use Opake\Auth\SessionProvider;
use OpakeAdmin\Model\Search\Organization as OrganizationSearch;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();

		if ($org_id = $this->request->get('org')) {
			$this->iniOrganization($org_id);
		}
	}

	public function actionIndex()
	{

		$items = [];
		$model = $this->orm->get('Organization');

		$search = new OrganizationSearch($this->pixie);
		$results = $search->search($model, $this->request);

		foreach ($results as $result) {
			$items[] = $result->toShortArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount()
		];
	}

	public function actionOrg()
	{
		$this->result = [];
		$q = $this->request->get('query');
		$onlyTitles = $this->request->get('only_titles');

		if ($q !== null) {
			$organization = $this->orm->get('organization')
				->where('name', 'like', '%' . $q . '%')
				->order_by('name', 'asc')
				->limit(12);
		} else {
			$organization = $this->orm->get('organization')
				->order_by('name', 'asc')
				->limit(12);
		}

		if ($onlyTitles) {
			foreach ($organization->find_all() as $org) {
				$this->result[] = $org->name;
			}
		} else {
			foreach ($organization->find_all() as $org) {
				$this->result[] = $org->getFormatter('SelectOptions')->toArray();
			}
		}
	}

	public function actionUser()
	{
		$this->result = [];
		$q = $this->request->get('query');
		$user = $this->orm->get('user');

		if ($q !== null) {
			$user->where([
				[$this->pixie->db->expr("CONCAT_WS(' ',first_name,last_name)"), 'like', '%' . $q . '%'],
				['or', ['email', 'like', '%' . $q . '%']]
			]);
		}

		if ($this->org) {
			$user->where('and', array('organization_id', $this->org->id));
		}
		$user->order_by('first_name', 'asc')->order_by('last_name', 'asc')->limit(12);

		foreach ($user->find_all() as $org) {
			$this->result[] = $org->getFullName();
		}
	}

	public function actionLocation()
	{
		$result = [];
		foreach ($this->org->getLocations() as $location) {
			$result[] = $location->getFormatter('SelectOptions')->toArray();
		}
		$this->result = $result;
	}

	public function actionStorage()
	{
		$this->result = [];
		$site = $this->request->get('site');

		$sites = $this->org->sites;
		$sites->where('active', 1);
		if ($site) {
			$sites->where('id', $site);
		}

		foreach ($sites->find_all() as $site) {
			foreach ($site->storage->find_all() as $location) {
				$this->result[] = $location->toArray();
			}
		}
	}

	public function actionRoles()
	{
		$this->result = [];
		foreach ($this->orm->get('Role')->find_all() as $role) {
			$this->result[] = $role->toArray();
		}
	}

	public function actionProfessions()
	{
		$this->result = [];
		foreach ($this->orm->get('Profession')->find_all() as $role) {
			$this->result[] = $role->toArray();
		}
	}

	public function actionDepartmentsList()
	{
		$this->result = [];

		$model = $this->orm->get('Department');
		$model->where('active', true);
		$model->order_by('name', 'asc');

		foreach ($model->find_all() as $department) {
			$this->result[] = $department->toArray();
		}
	}

	public function actionSite()
	{
		$this->result = [];
		$q = $this->request->get('query');

		if ($q !== null) {
			foreach ($this->services->get('user')->getSites($this->org->id, $q) as $site) {
				$this->result[] = $site->name;
			}
		} else {
			foreach ($this->org->sites->where('active', true)->find_all() as $site) {
				$this->result[] = $site->toArray();
			}
		}
	}

	public function actionDepartment()
	{
		$this->result = [];
		$sites = array_filter(explode(',', $this->request->get('query')));

		$departments = $this->services->get('user')->getDepartmentsBySites($this->org->id, $sites);
		foreach ($departments as $department) {
			$this->result[] = [
				'id' => $department->id,
				'name' => $department->name
			];
		}
	}

	public function actionRefreshExpires()
	{
		$sessionProvider = new SessionProvider($this->pixie);
		$sessionProvider->forceRefreshCurrentSession();

		$this->result = [
			'success' => true
		];
	}

	public function actionKeepActive()
	{
		$this->logged();

		$this->result = [
			'success' => true
		];
	}

	public function actionCheckLoggedIn()
	{
		$this->result = [
			'success' => true,
			'logged' => $this->pixie->auth->user() != null
		];
	}

}
