<?php

namespace OpakeAdmin\Model\Search\Billing\PatientStatement;

use Opake\Model\Search\AbstractSearch;

class History extends AbstractSearch
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
			'last_name' => trim($request->get('last_name')),
			'first_name' => trim($request->get('first_name')),
			'date_generated_from' => trim($request->get('date_generated_from')),
			'date_generated_to' => trim($request->get('date_generated_to')),
		];

		$query = $model->query;

		$query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `patient_statement_history`.*'))
			->join('patient', ['patient.id', $model->table . '.patient_id'])
			->where('patient.organization_id', $this->organizationId)
			->order_by($model->table . '.date_generated', 'desc');

		if (!empty($this->_params['last_name'])) {
			$model->where('patient.last_name', 'like', '%' . $this->_params['last_name'] . '%');
		}
		if (!empty($this->_params['first_name'])) {
			$model->where('patient.first_name', 'like', '%' . $this->_params['first_name'] . '%');
		}
		if (!empty($this->_params['date_generated_from'])) {
			$query->where($this->pixie->db->expr('DATE(date_generated)'), '>=', \Opake\Helper\TimeFormat::formatToDB($this->_params['date_generated_from']));
		}
		if (!empty($this->_params['date_generated_to'])) {
			$query->where($this->pixie->db->expr('DATE(date_generated)'), '<=', \Opake\Helper\TimeFormat::formatToDB($this->_params['date_generated_to']));
		}

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