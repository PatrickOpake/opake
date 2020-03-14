<?php

namespace OpakeAdmin\Model\Search\Patients;

use Opake\Model\Search\AbstractSearch;

class Users extends AbstractSearch
{
	public function search($model, $request)
	{
		$model = $this->prepare($model, $request);

		$this->_params = [
			'name' => trim($request->get('name')),
			'email' => trim($request->get('email')),
		];

		$sort = $request->get('sort_by', 'date');
		$order = $request->get('sort_order', 'DESC');

		$model->query
			->fields(
				$this->pixie->db->expr('DISTINCT SQL_CALC_FOUND_ROWS `patient_user`.*')
			)
			->join('patient', ['patient_user.patient_id', 'patient.id'], 'inner');

		$model->query
			->where('patient_user.password', 'IS NOT NULL', $this->pixie->db->expr(''));

		if (!empty($this->_params['name'])) {
			$model->query->where($this->pixie->db->expr("CONCAT_WS(' ', patient.first_name, patient.last_name)"), 'like', '%' . $this->_params['name'] . '%');
		}

		if (!empty($this->_params['email'])) {
			$model->query->where('patient.home_email',  'like', '%' . $this->_params['email'] . '%');
		}

		switch ($sort) {
			case 'name':
				$model->query->order_by('patient.first_name', $order);
				break;
			case 'email':
				$model->query->order_by('patient.home_email', $order);
				break;
			case 'organization':
				$model->query->join('organization', ['patient.organization_id', 'organization.id']);
				$model->query->order_by('organization.name', $order);
				break;
			case 'first_login_date':
				$model->query->order_by('patient_user.first_login_date', $order);
				break;
			case 'last_login_date':
				$model->query->order_by('patient_user.last_login_date', $order);
				break;
		}

		if ($this->_pagination) {
			$model->pagination($this->_pagination);
		}

		$results = $model->find_all();

		$count = $this->pixie->db
			->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');


		if ($this->_pagination) {
			$this->_pagination->setCount($count);
		}

		return $results;

	}
}