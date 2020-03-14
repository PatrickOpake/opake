<?php

namespace OpakeAdmin\Model\Search\Cases\OperativeReport;

use Opake\Model\Profession;
use Opake\Model\Search\AbstractSearch;

class Future extends AbstractSearch {

	/**
	 * Params
	 * @var array
	 */

	public function search($model, $request) {

		$model = parent::prepare($model, $request);

		$this->_params = [
		    'user_id' => trim($request->get('user_id')),
		    'case_type' => trim($request->get('case_type')),
		    'name' => trim($request->get('name')),
		    'updated' => trim($request->get('updated')),
		    'doctor' => trim($request->get('doctor')),

		];

		$sort = $request->get('sort_by', 'id');
		$order = $request->get('sort_order', 'DESC');

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->join(['case_type', 'ct'], [$model->table . '.cpt_id', 'ct.id']);

		$user = $this->pixie->auth->user();

		if($this->_params['user_id'] !== '' && $this->pixie->permissions->checkAccess('operative_reports', 'view')) {
			$model->query->join(['case_op_report_future_user', 'u'], [$model->table.'.id', 'u.report_id']);
			$model->where('u.user_id', $this->_params['user_id']);
		} else if(!$user->isInternal()) {
			if (!$this->pixie->permissions->getAccessLevel('operative_reports', 'view')->isAllowed()) {
				$model->query->join(['case_op_report_future_user', 'u'], [$model->table.'.id', 'u.report_id']);
				$model->where('u.user_id', $user->id);
			}
		}

		if ($this->_params['case_type'] !== '') {
			$model->where('ct.id', $this->_params['case_type']);
		}
		if ($this->_params['updated'] !== '') {
			$model->where($this->pixie->db->expr('DATE(' . $model->table . '.updated)'),  \Opake\Helper\TimeFormat::formatToDB($this->_params['updated']));
		}
		if ($this->_params['name'] !== '') {
				$model->where($model->table . '.name', 'like', '%' . $this->_params['name'] . '%');
		}

		switch ($sort) {
			case 'case_type': $model->order_by('ct.name', $order);
				break;
			case 'name': $model->order_by($model->table . '.name', $order);
				break;
			case 'updated':
				$model->order_by($model->table.'.updated', $order);
				break;
			case 'doctor':
				$model->query->join(['case_op_report_future_user', 'u'], [$model->table.'.id', 'u.report_id']);
				$model->query->join(['user', 'us'], ['us.id', 'u.user_id']);
				$model->order_by('us.first_name', $order)->order_by('us.last_name', $order);
				break;
		}

		$results = $model->pagination($this->_pagination)->find_all()->as_array();

		$count = $this->pixie->db
				->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
				->execute()->get('count');

		$this->_pagination->setCount($count);

		return $results;
	}

	public function getCountByUser($main_model, $user_id) {
		$model = $this->pixie->orm->get('Cases_OperativeReport_Future');
		$model->query = clone $main_model->query;
		$model->query->fields($this->pixie->db->expr($model->table.'.*'));

		if($user_id && $this->pixie->permissions->checkAccess('operative_reports', 'view')) {
			$model->query->join(['case_op_report_future_user', 'u'], [$model->table.'.id', 'u.report_id']);
			$model->where('u.user_id', $user_id);
		}

		return (int) $this->pixie->db
			->query('select')->fields($this->pixie->db->expr('COUNT(*) as count'))
			->table($model->query)
			->execute()->get('count');
	}
}