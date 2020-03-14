<?php

namespace OpakeAdmin\Model\Search;

use Opake\Helper\TimeFormat;
use Opake\Model\Search\AbstractSearch;

class Booking extends AbstractSearch
{

	public function search($model, $request)
	{

		$model = parent::prepare($model, $request);

		$this->_params = [
			'patient_first_name' => trim($request->get('patient_first_name')),
			'patient_last_name' => trim($request->get('patient_last_name')),
			'surgeons' => trim($request->get('surgeons')),
			'status' => trim($request->get('status')),
			'dateFrom' => trim($request->get('dateFrom')),
			'dateTo' => trim($request->get('dateTo')),
		];

		$sort = $request->get('sort_by', 'dos');
		$order = $request->get('sort_order', 'DESC');

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));

		if ($this->_params['status'] !== '') {
			if($this->_params['status'] == \Opake\Model\Booking::STATUS_SCHEDULED) {
				$model->where($model->table . '.status', \Opake\Model\Booking::STATUS_SCHEDULED);
			} else {
				$model->where('and', [
					['or', [$model->table . '.status', \Opake\Model\Booking::STATUS_NEW]],
					['or', [$model->table . '.status', \Opake\Model\Booking::STATUS_SUBMITTED]],
				]);
			}
		}

		if ($this->_params['patient_first_name'] !== '' || $this->_params['patient_last_name'] !== '') {
			$model->query->join('patient', [$model->table . '.patient_id', 'patient.id']);

			if ($this->_params['patient_first_name'] !== '') {
				$model->where('patient.first_name', 'like', '%' . $this->_params['patient_first_name'] . '%');
			}
			if ($this->_params['patient_last_name'] !== '') {
				$model->where('patient.last_name', 'like', '%' . $this->_params['patient_last_name'] . '%');
			}
		}

		if (!empty($this->_params['dateFrom'])) {
			$dateTime = TimeFormat::fromDBDate($this->_params['dateFrom']);
			if ($dateTime) {
				$dateTime->setTime(0, 0, 0);
				$model->query->where($this->pixie->db->expr('DATE('. $model->table . '.time_start)'), '>=', TimeFormat::formatToDBDatetime($dateTime));
			}
		}
		if (!empty($this->_params['dateTo'])) {
			$dateTime = TimeFormat::fromDBDate($this->_params['dateTo']);
			if ($dateTime) {
				$dateTime->setTime(23, 59, 59);
				$model->query->where($model->table . '.time_start', '<=', TimeFormat::formatToDBDatetime($dateTime));
			}
		}

		if ($this->_params['surgeons'] !== '') {
			$surgeonsIds = json_decode($this->_params['surgeons'], true);
			if (count($surgeonsIds)) {
				$model->query->join('booking_user', ['booking_sheet.id', 'booking_user.booking_id'])
					->where('booking_user.user_id', 'IN', $this->pixie->db->expr("(" . implode(',', $surgeonsIds) . ")"));
			}
		}

		$user = $this->pixie->auth->user();
		if ($user->isSatelliteOffice()) {
			$userPracticeGroupIds = $user->getPracticeGroupIds();
			if ($userPracticeGroupIds) {
				$model->query->join('booking_user', [$model->table . '.id', 'booking_user.booking_id']);
				$model->query->join(['user_practice_groups', 'upg'], ['booking_user.user_id', 'upg.user_id'], 'left');
				$model->where('and', [
					['or', ['upg.practice_group_id', 'IN', $this->pixie->db->expr("(" . implode(',', $userPracticeGroupIds) . ")")]],
					['or', ['booking_user.user_id', $user->id()]]
				]);
				$model->query->group_by($model->table . '.id');
			}
		} else {
			$model->where('and', [
				[
					[$model->table . '.is_updated_by_satellite', 1],
					[$model->table . '.status', \Opake\Model\Booking::STATUS_SUBMITTED]
				],
				[
					'or', [
						[$model->table . '.is_updated_by_satellite', 0],
					]
				]
			]);
		}

		switch ($sort) {
			case 'dos':
				$model->order_by($model->table . '.time_start', $order);
				break;

		}

		$results = $model->pagination($this->_pagination)->find_all()->as_array();

		$count = $this->pixie->db
			->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');

		$this->_pagination->setCount($count);

		return $results;
	}

}
