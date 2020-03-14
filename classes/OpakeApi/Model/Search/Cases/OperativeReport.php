<?php

namespace OpakeApi\Model\Search\Cases;

use Opake\Model\Profession;
use OpakeApi\Model\Search\AbstractSearch;

class OperativeReport extends AbstractSearch
{

	public function search($request)
	{
		$model = $this->pixie->orm->get('Cases_OperativeReport');
		$model->query->fields($this->pixie->db->expr($model->table . '.*'));

		$this->_params = [
			'type' => trim($request->get('type'))
		];

		$model->query->join('case', [$model->table . '.case_id', 'case.id'])
			->where('case.appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED)
			->group_by($model->table . '.id');

		$user = $this->pixie->auth->user();

		$this->queryUserAccess($model, $user);
		$this->querySurgeonType($model, $user);

		if ($this->_params['type'] !== '') {
			$this->queryAlert($model, $this->_params['type']);
		}

		if ($offset = $request->get('offset')) {
			$model->offset($offset);
		}

		if ($limit = $request->get('limit')) {
			$model->limit($limit);
		}

		return $model->order_by('case.time_start', 'desc')->find_all();
	}

	public function getCountByAlert()
	{
		$model = $this->pixie->orm->get('Cases_OperativeReport');
		$model->query->fields($this->pixie->db->expr($model->table . '.*'))
			->join('case', [$model->table . '.case_id', 'case.id'])
			->where('case.appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED)
			->group_by($model->table . '.id');

		$user = $this->pixie->auth->user();

		$this->queryUserAccess($model, $user);
		$this->querySurgeonType($model, $user);
		if ($this->_params['type'] !== '') {
			$this->queryAlert($model, $this->_params['type']);
		}

		return (int)$this->pixie->db
			->query('select')->fields($this->pixie->db->expr('COUNT(*) as count'))
			->table($model->query)
			->execute()->get('count');
	}

	protected function queryUserAccess($model, $user)
	{
		$model->query->where($model->table . '.surgeon_id', $user->id());
	}

	protected function querySurgeonType($model, $user)
	{
		if(\Opake\Model\Cases\OperativeReport::isNonSurgeonReport($user)) {
			$model->query->where($model->table . '.type', \Opake\Model\Cases\OperativeReport::TYPE_NON_SURGEON);
		} else {
			$model->query->where(
				$model->table . '.type', 'IN', $this->pixie->db->arr(\Opake\Model\Cases\OperativeReport::getTypeSurgeons())
			);
		}
	}

	protected function queryAlert($model, $type)
	{
		if ($type === 'open') {
			$model->where(
				[$model->table . '.status', \Opake\Model\Cases\OperativeReport::STATUS_OPEN],
				['or', [$model->table . '.status', \Opake\Model\Cases\OperativeReport::STATUS_DRAFT]]
			);
		} else if ($type === 'submitted') {
			$model->where([$model->table . '.status', \Opake\Model\Cases\OperativeReport::STATUS_SUBMITTED],
				['or', [$model->table . '.status', \Opake\Model\Cases\OperativeReport::STATUS_SIGNED]]);

		}

		$model->where($model->table . '.is_archived', 0);
	}
}
