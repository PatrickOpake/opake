<?php

namespace OpakeAdmin\Model\Search\Billing;

use Opake\Model\Billing\Navicure\Claim;
use Opake\Model\Insurance\AbstractType;
use Opake\Model\Search\AbstractSearch;

class ClaimsManagement extends AbstractSearch
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
		    'status' => trim($request->get('status')),
		    'type' => trim($request->get('type'))
		];

		$sort = $request->get('sort_by', 'claim_number');
		$order = $request->get('sort_order', 'DESC');

		$query = $model->query;

		$query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `billing_navicure_claim`.*'))
			->join('case', ['case.id', 'billing_navicure_claim.case_id'], 'inner')
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
			$query->where('billing_navicure_claim.dos', '>=', \Opake\Helper\TimeFormat::formatToDB($this->_params['dos_from']));
		}
		if (!empty($this->_params['dos_to'])) {
			$query->where('billing_navicure_claim.dos', '<=', \Opake\Helper\TimeFormat::formatToDB($this->_params['dos_to']));
		}
		if (!empty($this->_params['billing_date_from'])) {
			$query->where('billing_navicure_claim.last_transaction_date', '>=', \Opake\Helper\TimeFormat::formatToDB($this->_params['billing_date_from']));
		}
		if (!empty($this->_params['billing_date_to'])) {
			$query->where('billing_navicure_claim.last_transaction_date', '<=', \Opake\Helper\TimeFormat::formatToDB($this->_params['billing_date_to']));
		}
		if (!empty($this->_params['payer'])) {
			$query->where('and', [
				['insurance_data_regular.insurance_id', $this->_params['payer']],
				['or', ['insurance_data_auto_accident.insurance_company_id', $this->_params['payer']]],
				['or', ['insurance_data_workers_comp.insurance_company_id', $this->_params['payer']]],
			]);
		}
		if (!empty($this->_params['patient_first_name'])) {
			$query->where('billing_navicure_claim.first_name', 'like', '%' . $this->_params['patient_first_name'] . '%');
		}
		if (!empty($this->_params['patient_last_name'])) {
			$query->where('billing_navicure_claim.last_name', 'like', '%' . $this->_params['patient_last_name'] . '%');
		}
		if (!empty($this->_params['patient_dob'])) {
			$query->where('case_registration.dob', \Opake\Helper\TimeFormat::formatToDB($this->_params['patient_dob']));
		}
		if (!empty($this->_params['case_number'])) {
			$query->where('case.id', $this->_params['case_number']);
		}
		if (isset($this->_params['status']) && $this->_params['status'] !== null && $this->_params['status'] !== '') {
			$query->where('billing_navicure_claim.status', $this->_params['status']);
		}
		if (!empty($this->_params['claim_number'])) {
			$query->where('billing_navicure_claim.id', $this->_params['claim_number']);
		}
		if (!empty($this->_params['type'])) {
			$query->where('billing_navicure_claim.type', $this->_params['type']);
		}

		switch ($sort) {
			case 'claim_id':
				$query->order_by('billing_navicure_claim.id', $order);
				break;
			case 'patient_name':
				$query->order_by('billing_navicure_claim.last_name', $order)
					->order_by('billing_navicure_claim.first_name', $order);
				break;
			case 'mrn':
				$query->order_by('billing_navicure_claim.mrn', $order);
				break;
			case 'case':
				$query->order_by('billing_navicure_claim.case_id', $order);
				break;
			case 'dos':
				$query->order_by('billing_navicure_claim.dos', $order);
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
			case 'transaction_date':
				$query->order_by('billing_navicure_claim.last_transaction_date', $order);
				break;
			case 'status':
				$query->order_by($this->pixie->db->expr($this->getStatusSqlCase()), $order);
				break;
			case 'type':
				$query->order_by($this->pixie->db->expr($this->getTypeSqlCase()), $order);
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

	protected function getStatusSqlCase()
	{
		$sql = 'CASE `billing_navicure_claim`.`status` ';
		$statuses = Claim::getListOfStatusDescription();
		foreach ($statuses as $id => $desc) {
			$sql .= 'WHEN ' . $id . ' THEN \'' . $desc . '\' ';
		}
		$sql .= 'ELSE 1 END';

		return $sql;
	}

	protected function getTypeSqlCase()
	{
		$sql = 'CASE `billing_navicure_claim`.`type` ';
		$types = Claim::getListOfElectronicClaimType();
		foreach ($types as $id => $desc) {
			$sql .= 'WHEN ' . $id . ' THEN \'' . $desc . '\' ';
		}
		$sql .= 'ELSE 1 END';

		return $sql;
	}
}