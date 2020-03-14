<?php

namespace OpakeAdmin\Model\Search\Billing\ClaimsProcessing;

use Opake\Model\Billing\Navicure\Payment\Bunch as PaymentBunch;
use Opake\Model\Search\AbstractSearch;
use Opake\Model\Insurance\AbstractType;

class Bunch extends AbstractSearch
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

		$query = $model->query;
		$query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'));
		$query->where('organization_id', $this->getOrganizationId());

		$sort = $request->get('sort_by', 'id');
		$order = $request->get('sort_order', 'DESC');
		switch ($sort) {
			case 'eft_number':
				$query->order_by($this->pixie->db->expr('cast(eft_number as SIGNED)'), $order);
				break;
			case 'eft_date':
				$query->order_by('eft_date', $order)->order_by('id', $order);
				break;
			case 'balance':
				$query->order_by($this->pixie->db->expr('(
					`billing_navicure_payment_bunch`.`total_amount` 
					- `billing_navicure_payment_bunch`.`amount_paid` 
					- `billing_navicure_payment_bunch`.`patient_responsible_amount`
				)'), $order);
				break;
			case 'number_of_claims':
				$query->order_by($this->pixie->db->expr('(
					SELECT COUNT(1) 
					FROM `billing_navicure_payment`
					WHERE `billing_navicure_payment`.`payment_bunch_id` = `billing_navicure_payment_bunch`.`id`
				)'), $order);
				break;
			case 'status':
				$query->order_by($this->pixie->db->expr($this->getStatusSqlCase()), $order);
				break;
			case 'payer_name':
				$query->join([
					$this->pixie->db
						->query('select')
						->fields($this->pixie->db->expr('distinct claim_id, payment_bunch_id'))
						->table('billing_navicure_payment')
						->group_by('payment_bunch_id'), 'pay'
					], [
						$this->pixie->db->expr('pay.payment_bunch_id'),
						$this->pixie->db->expr('billing_navicure_payment_bunch.id')
				], 'left');

			    $query->join('billing_navicure_claim', [
						'pay.claim_id', 'billing_navicure_claim.id'
				], 'left');

				$query->join(['insurance_payor', 'p0'], [
					['p0.id', 'billing_navicure_claim.insurance_payer_id']
				],'left');

				// see OpakeAdmin\Model\Search\Billing\ClaimsManagement
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
				$query->order_by($this->pixie->db->expr('(IFNULL(p1.name, IFNULL(p2.name, IFNULL(p3.name, p0.name))))'), $order);
				break;
			default:
			    $query->order_by($sort, $order);
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
		$sql = 'CASE `billing_navicure_payment_bunch`.`status` ';
		$statuses = PaymentBunch::getStatusesList();
		foreach ($statuses as $id => $desc) {
			$sql .= 'WHEN ' . $id . ' THEN \'' . $desc . '\' ';
		}
		$sql .= 'ELSE 1 END';

		return $sql;
	}
}