<?php

namespace OpakeAdmin\Model\Search\Cases;

use Opake\Model\Search\AbstractSearch;

class InService extends AbstractSearch {

	public function search($model, $request) {

		$model = parent::prepare($model, $request);

		$this->_params = [
			'id' => trim($request->get('id')),
			'start' => trim($request->get('start')),
			'end' => trim($request->get('end')),
			'location' => trim($request->get('location')),
			'dos' => trim($request->get('dos')),
			'view_type' => trim($request->get('view_type')),
			'start_of_week' => trim($request->get('start_of_week')),
			'end_of_week' => trim($request->get('end_of_week')),
		];

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->group_by($model->table . '.id');

		if ($this->_params['location'] !== '' ) {
			$model->query->join('location', [$model->table . '.location_id', 'location.id']);
			$model->where('location.name', 'like', '%' . $this->_params['location'] . '%');
		}

		if ($this->_params['start'] !== '') {
			$model->where([$this->pixie->db->expr('DATE(case_in_service.start)'), '>=', $this->_params['start']]);
		}

		if ($this->_params['end'] !== '') {
			$model->where( [$this->pixie->db->expr('DATE(case_in_service.end)'), '<=', $this->_params['end']]);
		}

		if ($this->_params['dos'] !== '') {
			if ($this->_params['view_type'] == 'week') {
				$model->where($this->pixie->db->expr('DATE(case_in_service.start)'), '>=', \Opake\Helper\TimeFormat::formatToDB($this->_params['start_of_week']))
					->where($this->pixie->db->expr('DATE(case_in_service.start)'), '<=', \Opake\Helper\TimeFormat::formatToDB($this->_params['end_of_week']))
					->order_by('case_in_service.start', 'ASC');
			} else {
				$model->where($this->pixie->db->expr('DATE(case_in_service.start)'), \Opake\Helper\TimeFormat::formatToDB($this->_params['dos']))
					->order_by('case_in_service.start', 'ASC');
			}
		}

		$results = $model->find_all()->as_array();

		$count = $this->pixie->db
				->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
				->execute()->get('count');

		$this->_pagination->setCount($count);

		return $results;
	}

}
