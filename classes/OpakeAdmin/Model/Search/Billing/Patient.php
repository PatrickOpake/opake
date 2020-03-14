<?php

namespace OpakeAdmin\Model\Search\Billing;

use Opake\Model\Search\AbstractSearch;

class Patient extends AbstractSearch
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
			'first_name' => trim($request->get('first_name')),
			'last_name' => trim($request->get('last_name')),
			'mrn' => trim($request->get('mrn')),
			'dob' => trim($request->get('dob')),
			'exclude_patient' => trim($request->get('exclude_patient')),
			'insurance' => (int)$request->get('insurance'),
		];

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));

		$model->where($model->table . '.status', \Opake\Model\Patient::STATUS_ACTIVE);

		if ($this->_params['first_name'] !== '') {
			$model->where($model->table . '.first_name', 'like', '%' . $this->_params['first_name'] . '%');
		}

		if ($this->_params['last_name'] !== '') {
			$model->where($model->table . '.last_name', 'like', '%' . $this->_params['last_name'] . '%');
		}

		if ($this->_params['mrn'] !== '') {
			$model->query->where($this->pixie->db->expr("CONCAT(LPAD(patient.mrn, 5, '0'), '-', patient.mrn_year)"), 'like', '%' . $this->_params['mrn'] . '%');
		}

		if ($this->_params['dob'] !== '') {
			$model->where($this->pixie->db->expr('DATE(' . $model->table . '.dob)'), \Opake\Helper\TimeFormat::formatToDB($this->_params['dob']));
		}

		if (!empty($this->_params['exclude_patient'])) {
			$model->where($model->table . '.id', '!=', $this->_params['exclude_patient']);
		}

		if (!empty($this->_params['insurance'])) {
			$insuranceDataModel = $this->pixie->orm->get('Patient_Insurance', $this->_params['insurance'])->getInsuranceDataModel();

			$fieldName = isset($insuranceDataModel->insurance_id)
				? 'insurance_id'
				: (isset($insuranceDataModel->insurance_company_id)
					? 'insurance_company_id'
					: null);

			if ($fieldName && !is_null($insuranceDataModel->$fieldName)) {
				$model->where(
					$this->pixie->db->expr(''),
					'exists',
					$this->pixie->db->expr('(
						SELECT types.patient_id 
						FROM patient_insurance_types as types
							INNER JOIN ' . $insuranceDataModel->table . ' as insurances
								ON insurances.id=types.insurance_data_id 
								AND insurances.'. $fieldName .'=' . $insuranceDataModel->$fieldName . '
						WHERE patient.id=types.patient_id
						GROUP BY patient_id
					)')
				);
			}
		}

		if ($this->_pagination) {
			$model->pagination($this->_pagination);
		}
		if ($this->organizationId) {
			$model->where('organization_id', $this->organizationId);
		}

		$model->order_by('id', 'DESC');

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
