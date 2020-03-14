<?php

namespace OpakeAdmin\Model\Search\Billing\ItemizedBill;

use Opake\Model\Search\AbstractSearch;

class Patient extends AbstractSearch
{
	protected $organizationId;

	/**
	 * @return mixed
	 */
	public function getOrganizationId()
	{
		return $this->organizationId;
	}

	/**
	 * @param mixed $organizationId
	 */
	public function setOrganizationId($organizationId)
	{
		$this->organizationId = $organizationId;
	}

	public function search($model, $request)
	{

		$model = parent::prepare($model, $request);

		$this->_params = [
			'first_name' => trim($request->get('first_name')),
			'last_name' => trim($request->get('last_name')),
			'mrn' => trim($request->get('mrn')),
		];

		$sort = $request->get('sort_by', 'id');
		$order = $request->get('sort_order', 'ASC');

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->group_by($model->table . '.id');

		$model->where($model->table . '.status', \Opake\Model\Patient::STATUS_ACTIVE);

		if ($this->_params['first_name'] !== '') {
			$model->where($model->table . '.first_name', 'like', '%' . $this->_params['first_name'] . '%');
		}

		if ($this->_params['last_name'] !== '') {
			$model->where($model->table . '.last_name', 'like', '%' . $this->_params['last_name'] . '%');
		}

		if ($this->_params['mrn'] !== '') {
			$model->query->where($this->pixie->db->expr("CONCAT(LPAD(patient.mrn, 5, '0'), '-', patient.mrn_year)"), 'like', '%' . $this->_params['mrn'] . '%');
		}

		switch ($sort) {
			case 'id':
				$model->order_by($model->table . '.id', $order);
				break;
			case 'first_name':
				$model->order_by($model->table . '.first_name', $order);
				break;
			case 'last_name':
				$model->order_by($model->table . '.last_name', $order);
				break;
			case 'mrn':
				$model->order_by($model->table . '.mrn', $order);
				break;
		}

		if ($this->_pagination) {
			$model->pagination($this->_pagination);
		}
		if ($this->organizationId) {
			$model->where('organization_id', $this->organizationId);
		}

		$model->order_by('id', 'DESC');

		$results = $model->find_all()->as_array();

		$count = $this->pixie->db
			->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');

		if ($this->_pagination) {
			$this->_pagination->setCount($count);
		}

		return $results;
	}

}
