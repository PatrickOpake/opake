<?php

namespace OpakeAdmin\Model\Search\Billing;

use Opake\Helper\TimeFormat;
use Opake\Model\Search\AbstractSearch;

class CasesReport extends AbstractSearch
{
	public function search($model, $request) {

		$model = parent::prepare($model, $request);

		$this->_params = [
		    'id' => trim($request->get('id')),
		    'dateFrom' => trim($request->get('dateFrom')),
		    'dateTo' => trim($request->get('dateTo')),
		    'cpt' => trim($request->get('cpt')),
			'surgeons' => trim($request->get('surgeons')),
			'practices' => trim($request->get('practices')),
			'insurance' => trim($request->get('insurance'))
		];

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->join('case', [$model->table . '.case_id', 'case.id'])
			->join('case_user', ['case.id', 'case_user.case_id'])
			->join(['user_practice_groups', 'upg'], ['case_user.user_id', 'upg.user_id'], 'left')
			->group_by($model->table . '.id');

		if (!empty($this->_params['dateFrom'])) {
			$dateTime = TimeFormat::fromDBDate($this->_params['dateFrom']);
			if ($dateTime) {
				$dateTime->setTime(0, 0, 0);
				$model->query->where('and', [
					['or', [$this->pixie->db->expr('DATE(case.time_start)'), '>=', TimeFormat::formatToDBDatetime($dateTime)]],
					['or', [$this->pixie->db->expr('DATE(' . $model->table . '.dos)'), '>=', TimeFormat::formatToDBDatetime($dateTime)]],
				]);
			}
		}
		if (!empty($this->_params['dateTo'])) {
			$dateTime = TimeFormat::fromDBDate($this->_params['dateTo']);
			if ($dateTime) {
				$dateTime->setTime(23, 59, 59);
				$model->query->where('and', [
					['or', [$this->pixie->db->expr('DATE(case.time_start)'), '<=', TimeFormat::formatToDBDatetime($dateTime)]],
					['or', [$this->pixie->db->expr('DATE(' . $model->table . '.dos)'), '<=', TimeFormat::formatToDBDatetime($dateTime)]],
				]);			}
		}

		if ($this->_params['surgeons'] !== '') {
			$surgeonsIds = json_decode($this->_params['surgeons'], true);
			if (count($surgeonsIds)) {
				$model->query->where('case_user.user_id', 'IN', $this->pixie->db->arr($surgeonsIds));
			}
		}

		if ($this->_params['practices'] !== '') {
			$practicesIds = json_decode($this->_params['practices'], true);
			if (count($practicesIds)) {
				$model->query->where('upg.practice_group_id', 'IN', $this->pixie->db->arr($practicesIds));
			}
		}

		if ($this->_params['insurance'] !== '') {
			$model->query->join('case_registration', ['case.id', 'case_registration.case_id'])
			->join(['case_registration_insurance_types', 'crit'], ['case_registration.id', 'crit.registration_id'])
			->join(['insurance_data_regular', 'idr'], ['crit.insurance_data_id', 'idr.id'])
			->where('idr.insurance_id', $this->_params['insurance']);
		}

		if (!empty($this->_params['cpt'])) {
			$model->query->where($model->table . '.cpt', 'like', '%' . $this->_params['cpt'] . '%');
		}

		$model->query->order_by($model->table . '.at_top', 'desc')
			->order_by($this->pixie->db->expr('IFNULL(`dos`, `time_start`)'), 'desc');


		$this->pixie->db->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');

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