<?php

namespace OpakeAdmin\Model\Search\Cases\Blocking;

use Opake\Model\Search\AbstractSearch;

class Item extends AbstractSearch {

	public function search($model, $request) {

		$model = parent::prepare($model, $request);

		$this->_params = [
		    'id' => trim($request->get('id')),
		    'start' => trim($request->get('start')),
		    'end' => trim($request->get('end')),
			'location' => trim($request->get('location')),
			'doctor' =>  trim($request->get('doctor'))
		];

		if ($this->pixie->permissions->getAccessLevel('case_blocks', 'view')->isSelfAllowed()) {
			$user = $this->pixie->auth->user();

			$model->where('doctor_id', $user->id);
		}

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->group_by('case_blocking_item.id');

		if ($this->_params['location'] !== '' ) {
			$model->query->join('location', [$model->table . '.location_id', 'location.id']);
			$model->where('location.name', 'like', '%' . $this->_params['location'] . '%');
		}

		if ($this->_params['doctor'] !== '') {
			$model->where('doctor_id', $this->_params['doctor']);
		}

		if ($this->_params['start'] !== '') {
			$model->where([$this->pixie->db->expr('DATE(case_blocking_item.start)'), '>=', $this->_params['start']]);
		}

		if ($this->_params['end'] !== '') {
			$model->where( [$this->pixie->db->expr('DATE(case_blocking_item.end)'), '<=', $this->_params['end']]);
		}

		$results = $model->find_all()->as_array();

		$count = $this->pixie->db
				->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
				->execute()->get('count');

		$this->_pagination->setCount($count);

		return $results;
	}

}
