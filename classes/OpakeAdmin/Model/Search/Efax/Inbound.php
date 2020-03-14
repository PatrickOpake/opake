<?php

namespace OpakeAdmin\Model\Search\Efax;

use Opake\Model\Search\AbstractSearch;

class Inbound extends AbstractSearch
{
	protected $user;

	/**
	 * @return mixed
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param mixed $user
	 */
	public function setUser($user)
	{
		$this->user = $user;
	}

	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);

		$db = $this->pixie->db;

		$this->_params = [
			'filter' => trim($request->get('filter')),
			'site_id' => trim($request->get('site_id'))
		];

		$user = $this->getUser();

		if (!$user) {
			throw new \Exception('User is required to get faxes');
		}

		$userSiteIds = [];
		foreach ($user->sites->find_all() as $site) {
			$userSiteIds[] = $site->id();
		}

		if (!$userSiteIds) {
			if ($this->_pagination) {
				$this->_pagination->setCount(0);
			}

			return [];
		}

		$query = $model->query;
		$query->fields($db->expr('SQL_CALC_FOUND_ROWS `efax_inbound_fax`.*'));
		$query->where('efax_inbound_fax.site_id', 'IN', $db->arr($userSiteIds));

		if (!empty($this->_params['filter'])) {
			$filter = $this->_params['filter'];

			if ($filter === 'read-only') {
				$query->where('efax_inbound_fax.is_read', 1);
			} else if ($filter === 'unread-only') {
				$query->where('efax_inbound_fax.is_read', 0);
			} else if ($filter === 'site' && !empty($this->_params['site_id'])) {
				$query->where('site_id', $this->_params['site_id']);
			}

		}

		$query->order_by('id', 'desc');

		if ($this->_pagination) {
			$model->pagination($this->_pagination);
		}

		$results = $model->find_all()
			->as_array();

		$count = $db->query('select')
			->fields($db->expr('FOUND_ROWS() as count'))
			->execute()
			->get('count');

		if ($this->_pagination) {
			$this->_pagination->setCount($count);
		}

		return $results;
	}


	public function getUnreadCount($model)
	{
		$user = $this->getUser();

		$db = $this->pixie->db;

		$userSiteIds = [];
		foreach ($user->sites->find_all() as $site) {
			$userSiteIds[] = $site->id();
		}

		if (!$userSiteIds) {
			return 0;
		}

		$query = $model->query;
		$query->where('efax_inbound_fax.site_id', 'IN', $db->arr($userSiteIds));
		$query->where('efax_inbound_fax.is_read', 0);

		return $model->count_all();
	}

}