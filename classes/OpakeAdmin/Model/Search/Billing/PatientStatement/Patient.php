<?php

namespace OpakeAdmin\Model\Search\Billing\PatientStatement;

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
			'original_dos_from' => trim($request->get('original_dos_from')),
			'original_dos_to' => trim($request->get('original_dos_to')),
		];

		$sort = $request->get('sort_by', 'original_dos');
		$order = $request->get('sort_order', 'ASC');


		$originalDosQuery = '
		SELECT 
  			`case`.`time_start`
		FROM
  			`case`
		LEFT JOIN
		  `case_registration`
		ON
		  `case_registration`.`case_id` = `case`.`id`
		  WHERE `case_registration`.`patient_id` = ' . $model->table . '.id  
		  ORDER BY `case`.`time_start`  ASC LIMIT 1
		';

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*,
			 ('.$originalDosQuery.') as original_dos, 
			 SUM(case_coding_bill.amount) as total_charges_amount, 
			 SUM(IFNULL(applied_payment.amount, 0)) as applied_amount,  
			 (SUM(case_coding_bill.amount) - SUM(IFNULL(applied_payment.amount, 0))) as outstanding_balance,
			 ledger_patient_outstanding_balance(patient.id) as outstanding_patient_responsible_balance'))
			->group_by($model->table . '.id');

		$model->query->join('case_registration', ['case_registration.patient_id', 'patient.id']);
		$model->query->join('case', ['case.id', 'case_registration.case_id']);
		$model->query->join('case_coding', ['case.id', 'case_coding.case_id']);
		$model->query->join('case_coding_bill', ['case_coding_bill.coding_id', 'case_coding.id'], 'right');
		$model->query->join(['billing_ledger_applied_payment', 'applied_payment'], ['case_coding_bill.id', 'applied_payment.coding_bill_id'], 'left');

		$model->where($model->table . '.status', \Opake\Model\Patient::STATUS_ACTIVE);
		$model->query->having([
			[$this->pixie->db->expr('total_charges_amount'), '>', $this->pixie->db->expr('applied_amount')],
			['or', [$this->pixie->db->expr('total_charges_amount'), '>', 0]],
		]);

		if ($this->_params['first_name'] !== '') {
			$model->where($model->table . '.first_name', 'like', '%' . $this->_params['first_name'] . '%');
		}

		if ($this->_params['last_name'] !== '') {
			$model->where($model->table . '.last_name', 'like', '%' . $this->_params['last_name'] . '%');
		}

		if ($this->_params['mrn'] !== '') {
			$model->query->where($this->pixie->db->expr("CONCAT(LPAD(patient.mrn, 5, '0'), '-', patient.mrn_year)"), 'like', '%' . $this->_params['mrn'] . '%');
		}

		if ($this->_params['original_dos_from'] !== '') {
			$model->query->having($this->pixie->db->expr('DATE(original_dos)'), '>=', \Opake\Helper\TimeFormat::formatToDB($this->_params['original_dos_from']));
		}
		if ($this->_params['original_dos_to'] !== '') {
			$model->query->having($this->pixie->db->expr('DATE(original_dos)'), '<=', \Opake\Helper\TimeFormat::formatToDB($this->_params['original_dos_to']));
		}

		$model->query->having($this->pixie->db->expr('outstanding_patient_responsible_balance'), 'IS NOT NULL', $this->pixie->db->expr(''));
		$model->query->having($this->pixie->db->expr('outstanding_patient_responsible_balance'), '>', 0);


		switch ($sort) {
			case 'id':
				$model->order_by($model->table . '.id', $order);
				break;
			case 'first_name':
				$model->order_by($model->table . '.first_name', $order);
				break;
			case 'last_name':
				$model->order_by($model->table . '.last_name', $order);
				break;
			case 'mrn':
				$model->order_by($model->table . '.mrn', $order);
				break;
			case 'original_dos':
				$model->order_by($this->pixie->db->expr('original_dos'), $order);
				break;
			case 'outstanding_balance':
				$model->order_by($this->pixie->db->expr('outstanding_balance'), $order);
				break;
			case 'dob':
				$model->order_by($model->table . '.dob', $order);
				break;
			case 'patient_responsibility_balance':
				$model->order_by($this->pixie->db->expr('outstanding_patient_responsible_balance'), $order);
				break;
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
