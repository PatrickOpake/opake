<?php

namespace OpakeAdmin\Model\Search\Billing;

use Opake\Model\Billing\Navicure\Claim;
use Opake\Model\Insurance\AbstractType;
use Opake\Model\Search\AbstractSearch;

class PaperClaims extends AbstractSearch
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
			'billing_date_from' => trim($request->get('billing_date_from')),
			'billing_date_to' => trim($request->get('billing_date_to')),
			'payer' => trim($request->get('payer')),
			'patient_last_name' => trim($request->get('patient_last_name')),
			'patient_first_name' => trim($request->get('patient_first_name')),
		    'patient_dob' => trim($request->get('patient_dob')),
		    'case_number' => trim($request->get('case_number')),
		    'claim_number' => trim($request->get('claim_number')),
		    'type' => trim($request->get('type'))
		];

		$sort = $request->get('sort_by', 'claim_number');
		$order = $request->get('sort_order', 'DESC');

		$query = $model->query;

		$query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `'.$model->table.'`.*'))
			->join('case', ['case.id', $model->table. '.case_id'], 'inner')
			->join('case_registration', ['case.id', 'case_registration.case_id'])
			->where('case.organization_id', $this->organizationId);

		$query->join('billing_navicure_claim_insurance_types', ['billing_navicure_claim.primary_insurance_id', 'billing_navicure_claim_insurance_types.id'], 'left');
		$query->join('insurance_data_auto_accident', [
			'and', [
				['billing_navicure_claim_insurance_types.insurance_data_id', 'insurance_data_auto_accident.id'],
				['and', ['billing_navicure_claim_insurance_types.type', $this->pixie->db->expr(AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT)]]
			]
		], 'left');
		$query->join('insurance_data_workers_comp', [
			'and', [
				['billing_navicure_claim_insurance_types.insurance_data_id', 'insurance_data_workers_comp.id'],
				['and', ['billing_navicure_claim_insurance_types.type', $this->pixie->db->expr(AbstractType::INSURANCE_TYPE_WORKERS_COMP)]]
			]
		], 'left');
		$query->join('insurance_data_regular', [
			'and', [
				['billing_navicure_claim_insurance_types.insurance_data_id', 'insurance_data_regular.id'],
				['and', ['billing_navicure_claim_insurance_types.type', 'IN', $this->pixie->db->arr(AbstractType::getRegularInsuranceTypeIds())]]
			]
		], 'left');

		if (!empty($this->_params['dos_from'])) {
			$query->where('case.time_start', '>=', \Opake\Helper\TimeFormat::formatToDB($this->_params['dos_from']));
		}
		if (!empty($this->_params['dos_to'])) {
			$query->where('case.time_start', '<=', \Opake\Helper\TimeFormat::formatToDB($this->_params['dos_to']));
		}
		if (!empty($this->_params['billing_date_from'])) {
			$query->where($model->table.'.last_transaction_date', '>=', \Opake\Helper\TimeFormat::formatToDB($this->_params['billing_date_from']));
		}
		if (!empty($this->_params['billing_date_to'])) {
			$query->where($model->table.'.last_transaction_date', '<=', \Opake\Helper\TimeFormat::formatToDB($this->_params['billing_date']));
		}
		if (!empty($this->_params['payer'])) {
			$query->where('and', [
				['insurance_data_regular.insurance_id', $this->_params['payer']],
				['or', ['insurance_data_auto_accident.insurance_company_id', $this->_params['payer']]],
				['or', ['insurance_data_workers_comp.insurance_company_id', $this->_params['payer']]],
			]);
		}
		if (!empty($this->_params['patient_first_name'])) {
			$query->where('case_registration.first_name', 'like', '%' . $this->_params['patient_first_name'] . '%');
		}
		if (!empty($this->_params['patient_last_name'])) {
			$query->where('case_registration.last_name', 'like', '%' . $this->_params['patient_last_name'] . '%');
		}
		if (!empty($this->_params['patient_dob'])) {
			$query->where('case_registration.dob', \Opake\Helper\TimeFormat::formatToDB($this->_params['patient_dob']));
		}
		if (!empty($this->_params['case_number'])) {
			$query->where('case.id', $this->_params['case_number']);
		}
		if (isset($this->_params['type']) && $this->_params['type'] !== null && $this->_params['type'] !== '') {
			$query->where($model->table.'.type', $this->_params['type']);
		}
		if (!empty($this->_params['claim_number'])) {
			$query->where($model->table.'.id', $this->_params['claim_number']);
		}

		switch ($sort) {
			case 'claim_id':
				$query->order_by($model->table.'.id', $order);
				break;
			case 'patient_name':
				$query->order_by('case_registration.last_name', $order)
					->order_by('case_registration.first_name', $order);
				break;
			case 'case':
				$query->order_by($model->table.'.case_id', $order);
				break;
			case 'dos':
				$query->order_by('case.time_start', $order);
				break;
			case 'dob':
				$query->order_by('case_registration.dob', $order);
				break;
			case 'payer':
				$query->join(['insurance_payor', 'p1'], [
					'and', [
						['p1.id', 'insurance_data_auto_accident.insurance_company_id'],
						['and', ['insurance_data_auto_accident.insurance_company_id' , 'IS NOT NULL', $this->pixie->db->expr('')]]
					]
				], 'left');
				$query->join(['insurance_payor', 'p2'], [
					'and', [
						['p2.id', 'insurance_data_workers_comp.insurance_company_id'],
						['and', ['insurance_data_workers_comp.insurance_company_id' , 'IS NOT NULL', $this->pixie->db->expr('')]]
					]
				], 'left');
				$query->join(['insurance_payor', 'p3'], [
					'and', [
						['p3.id', 'insurance_data_regular.insurance_id'],
						['and', ['insurance_data_regular.insurance_id' , 'IS NOT NULL', $this->pixie->db->expr('')]]
					]
				], 'left');
				$query->order_by($this->pixie->db->expr('(IFNULL(p1.name, IFNULL(p2.name, p3.name)))'), $order);
				break;
			case 'billing_date':
				$query->order_by($model->table.'.last_transaction_date', $order);
				break;
			case 'type':
				$query->order_by($model->table.'.type', $order);
				break;
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