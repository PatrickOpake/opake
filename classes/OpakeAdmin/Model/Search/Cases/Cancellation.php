<?php

namespace OpakeAdmin\Model\Search\Cases;

use Opake\Helper\TimeFormat;
use Opake\Model\Search\AbstractSearch;

class Cancellation extends AbstractSearch
{

	public function search($model, $request)
	{

		$model = parent::prepare($model, $request);

		$this->_params = [
			'id' => trim($request->get('id')),
			'org_id' => trim($request->param('id')),
			'dos' => trim($request->get('dos')),
			'patient_name' => trim($request->get('patient_name')),
			'cancel_date' => trim($request->get('cancel_date')),
			'cancel_date_from' => trim($request->get('cancel_date_from')),
			'cancel_date_to' => trim($request->get('cancel_date_to')),
			'rescheduled_date' => trim($request->get('rescheduled_date')),
			'patient_first_name' => trim($request->get('patient_first_name')),
			'patient_last_name' => trim($request->get('patient_last_name')),
			'cancel_status' => trim($request->get('cancel_status')),
			'mrn' => trim($request->get('mrn')),
			'cancelled_user' => trim($request->get('cancelled_user'))
		];

		$sort = $request->get('sort_by', 'cancel_date');
		$order = $request->get('sort_order', 'DESC');

		$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->group_by('case_cancellation.id');

		$model->query->join('case', [$model->table . '.case_id', 'case.id'])
			->where('case.organization_id', $this->_params['org_id']);
		$model->query->join('case_registration', ['case.id', 'case_registration.case_id']);
		$model->query->join('patient', ['case_registration.patient_id', 'patient.id']);

		$model->query->join('case_user', ['case.id', 'case_user.case_id']);
		$model->query->join('user', ['case_user.user_id', 'user.id']);

		if ($this->pixie->permissions->getAccessLevel('cases', 'view')->isSelfAllowed()) {
			$user = $this->pixie->auth->user();

			$model->query->join(['case_user', 'cu'], ['case.id', 'cu.case_id'], 'inner');
			$model->where('cu.user_id', $user->id);
		}

		if ($this->_params['dos'] !== '') {
			$model->where($this->pixie->db->expr('DATE(case_cancellation.dos)'), \Opake\Helper\TimeFormat::formatToDB($this->_params['dos']))
				->order_by('case_cancellation.dos', 'ASC');
		}

		if (!empty($this->_params['cancel_date_from'])) {
			$dateTime = TimeFormat::fromDBDate($this->_params['cancel_date_from']);
			if ($dateTime) {
				$dateTime->setTime(0, 0, 0);
				$model->query->where($this->pixie->db->expr('DATE(case_cancellation.cancel_time)'), '>=', TimeFormat::formatToDBDatetime($dateTime));
			}
		}
		if (!empty($this->_params['cancel_date_to'])) {
			$dateTime = TimeFormat::fromDBDate($this->_params['cancel_date_to']);
			if ($dateTime) {
				$dateTime->setTime(23, 59, 59);
				$model->query->where('case_cancellation.cancel_time', '<=', TimeFormat::formatToDBDatetime($dateTime));
			}
		}

		if ($this->_params['id'] !== '') {
			$model->where($model->table . '.id', $this->_params['id']);
		}

		if ($this->_params['patient_name'] !== '') {
			$model->query->where($this->pixie->db->expr("CONCAT(patient.last_name, ' ', patient.first_name)"), 'like', '%' . $this->_params['patient_name'] . '%');
		}

		if (($this->_params['patient_first_name'] !== '') || ($this->_params['patient_last_name'] !== '')) {
			$model->query->where('patient.first_name', 'like', '%' . $this->_params['patient_first_name'] . '%')
				->where('patient.last_name', 'like', '%' . $this->_params['patient_last_name'] . '%');
		}

		if ($this->_params['cancel_status'] !== '') {
			$model->where($model->table . '.cancel_status', $this->_params['cancel_status']);
		}

		switch ($sort) {
			case 'id':
				$model->order_by($model->table . '.id', $order);
				break;
			case 'dos':
				$model->order_by($model->table . '.dos', $order);
				break;
			case 'patient_name':
				$model->order_by('patient.last_name', $order)
					->order_by('patient.first_name', $order)
					->order_by($model->table . '.dos', $order);
				break;
			case 'mrn':
				$model->order_by('patient.mrn', $order)
					->order_by($model->table . '.dos', $order);
				break;
			case 'cancel_date':
				$model->order_by($model->table . '.cancel_time', $order);
				break;
			case 'rescheduled_date':
				$model->order_by($model->table . '.rescheduled_date', $order);
				break;
		}

		if ($this->_pagination) {
			$model->pagination($this->_pagination);
		}
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
