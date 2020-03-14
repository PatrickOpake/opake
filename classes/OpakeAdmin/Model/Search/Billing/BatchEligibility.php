<?php

namespace OpakeAdmin\Model\Search\Billing;

use Opake\Model\Search\AbstractSearch;

class BatchEligibility extends AbstractSearch
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
			'dos_from' => trim($request->get('dos_from')),
			'dos_to' => trim($request->get('dos_to')),
			'site' => trim($request->get('site')),
			'user' => trim($request->get('user')),
		];

		$query = $model->query;

		$query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `case_registration_insurance_types`.*'))
			->join('case_registration', ['case_registration.id', 'case_registration_insurance_types.registration_id'], 'inner')
			->join('case', ['case.id', 'case_registration.case_id'], 'inner')
			->where('case.appointment_status', '!=', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_CANCELED)
			->where('case.organization_id', $this->organizationId);

		if (!empty($this->_params['dos_from'])) {
			$query->where($this->pixie->db->expr('DATE(case.time_start)'), '>=', $this->_params['dos_from']);
		}
		if (!empty($this->_params['dos_to'])) {
			$query->where($this->pixie->db->expr('DATE(case.time_start)'), '<=', $this->_params['dos_to']);
		}
		if (!empty($this->_params['site'])) {
			$query->join('location', ['case.location_id', 'location.id']);
			$query->join('site', ['location.site_id', 'site.id']);
			$model->where('site.name', 'like', '%' . $this->_params['site'] . '%');
		}
		if (!empty($this->_params['user'])) {
			$model->query->join('case_user', ['case.id', 'case_user.case_id']);
			$model->query->join('user', ['case_user.user_id', 'user.id']);

			$model->query->join('case_other_staff', ['case.id', 'case_other_staff.case_id'], 'left');
			$model->query->join('case_co_surgeon', ['case.id', 'case_co_surgeon.case_id'], 'left');
			$model->query->join('case_surgeon_assistant', ['case.id', 'case_surgeon_assistant.case_id'], 'left');
			$model->query->join('case_supervising_surgeon', ['case.id', 'case_supervising_surgeon.case_id'], 'left');
			$model->query->join('case_first_assistant_surgeon', ['case.id', 'case_first_assistant_surgeon.case_id'], 'left');
			$model->query->join('case_assistant', ['case.id', 'case_assistant.case_id'], 'left');
			$model->query->join('case_anesthesiologist', ['case.id', 'case_anesthesiologist.case_id'], 'left');
			$model->query->join('case_dictated_by', ['case.id', 'case_dictated_by.case_id'], 'left');

			$model->where('and', [
				[$this->pixie->db->expr("CONCAT_WS(' ',user.first_name,user.last_name)"), 'like', '%' . $this->_params['user'] . '%']
			]);
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