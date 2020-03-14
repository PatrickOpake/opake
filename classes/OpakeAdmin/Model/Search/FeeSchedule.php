<?php

namespace OpakeAdmin\Model\Search;

use Opake\Model\Search\AbstractSearch;

class FeeSchedule extends AbstractSearch
{
	/**
	 * @var int
	 */
	protected $organizationId = null;

	/**
	 * @var int
	 */
	protected $siteId = null;

	/**
	 * @return int
	 */
	public function getOrganizationId()
	{
		return $this->organizationId;
	}

	/**
	 * @param int $organizationId
	 */
	public function setOrganizationId($organizationId)
	{
		$this->organizationId = $organizationId;
	}

	/**
	 * @return int
	 */
	public function getSiteId()
	{
		return $this->siteId;
	}

	/**
	 * @param int $siteId
	 */
	public function setSiteId($siteId)
	{
		$this->siteId = $siteId;
	}

	/**
	 * @param $model
	 * @param $request
	 * @return mixed
	 */
	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);

		$this->_params = [
			'hcpcs' => trim($request->get('hcpcs')),
			'type' => trim($request->get('type')),
		];

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->group_by($model->table . '.id');

		$model->query->where('organization_id', $this->getOrganizationId());
		$model->query->where('site_id', $this->getSiteId());

		if (!empty($this->_params['hcpcs'])) {
			$model->query
				->where('hcpcs', $this->_params['hcpcs']);
		}

		if (!empty($this->_params['type'])) {
			$model->query
				->where('type', $this->_params['type']);
		}

		$results = $model->pagination($this->_pagination)
			->find_all()
			->as_array();

		$count = $this->pixie->db
			->query('select')
			->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()
			->get('count');

		$this->_pagination->setCount($count);

		return $results;
	}
}
