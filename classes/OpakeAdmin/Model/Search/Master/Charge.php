<?php

namespace OpakeAdmin\Model\Search\Master;

use Opake\Model\Search\AbstractSearch;

class Charge extends AbstractSearch
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

	public function search($model, $request) {

		$model = parent::prepare($model, $request);

		$this->_params = [
		    'name' => trim($request->get('name')),
		    'cdm' => trim($request->get('cdm')),
		    'department' => trim($request->get('department')),
		    'amount_from' => trim($request->get('amount_from')),
		    'amount_to' => trim($request->get('amount_to')),
		    'cpt' => trim($request->get('cpt')),
		];

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->group_by($model->table . '.id');

		$model->query->where('site_id', $this->getSiteId());
		$model->query->where('archived', 0);

		if ($this->_params['name'] !== '') {
			$model->where('desc', 'like', '%' . $this->_params['name'] . '%');
		}
		if ($this->_params['cdm'] !== '') {
			$model->where('cdm', $this->_params['cdm']);
		}
		if ($this->_params['cpt'] !== '') {
			$model->where('cpt', $this->_params['cpt']);
		}
		if ($this->_params['department'] !== '') {
			$model->where('department', $this->_params['department']);
		}
		if ($this->_params['amount_from'] !== '') {
			$model->where('amount', '>=', $this->_params['amount_from']);
		}
		if ($this->_params['amount_to'] !== '') {
			$model->where('amount', '<=', $this->_params['amount_to']);
		}

		$model->order_by($model->table . '.order', 'asc');

		if ($this->_pagination) {
			$model->pagination($this->_pagination);
		}

		$results = $model->find_all()->as_array();

		$count = $this->pixie->db
			->query('select')
			->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()
			->get('count');

		if ($this->_pagination) {
			$this->_pagination->setCount($count);
		}

		return $results;
	}
}
