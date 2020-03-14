<?php

namespace OpakeAdmin\Model\Search\Billing\PaymentPosting;

use Opake\Model\Search\AbstractSearch;

class Bills extends AbstractSearch
{

	/**
	 * @var int
	 */
	protected $patientId;

	/**
	 * @return int
	 */
	public function getPatientId()
	{
		return $this->patientId;
	}

	/**
	 * @param int $patientId
	 */
	public function setPatientId($patientId)
	{
		$this->patientId = $patientId;
	}

	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);

		$query = $model->query;
		$query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `case_coding_bill`.*'));
		$query->join('case_coding', ['case_coding.id', 'case_coding_bill.coding_id']);
		$query->join('case', ['case.id', 'case_coding.case_id']);
		$query->join('case_registration', ['case_registration.case_id', 'case.id']);
		$query->where('case_registration.patient_id', $this->getPatientId());

		$query->order_by('case.time_start', 'DESC');
		$query->order_by('id', 'DESC');

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