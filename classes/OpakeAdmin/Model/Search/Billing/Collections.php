<?php

namespace OpakeAdmin\Model\Search\Billing;

use Opake\Model\Billing\Ledger\PaymentInfo;
use Opake\Model\Insurance\AbstractType;
use Opake\Model\Search\AbstractSearch;

class Collections extends AbstractSearch
{

	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);

		$this->_params = [
			'mrn' => trim($request->get('mrn')),
			'dosFrom' => trim($request->get('dosFrom')),
			'dosTo' => trim($request->get('dosTo')),
			'payer_type' => trim($request->get('payer_type')),
			'payer_name' => trim($request->get('payer_name')),
			'patientLastName' => trim($request->get('patientLastName')),
			'patientFirstName' => trim($request->get('patientFirstName')),
			'surgeon' => trim($request->get('surgeon')),
			'charge' => trim($request->get('charge')),
			'billing_status' => trim($request->get('billing_status')),
		];

		$sort = $request->get('sort_by', 'dos');
		$order = $request->get('sort_order', 'DESC');

		$query = $model->query;


		$query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `'. $model->table .'`.*'))
			->join('case_registration', ['case.id', 'case_registration.case_id'])
			->group_by($model->table . '.id');

		$user = $this->pixie->auth->user();
		if($user->isDoctor()) {
			$model->query->join('case_user', ['case.id', 'case_user.case_id'])
				->where('case_user.user_id', $user->id());
		}

		$usedJoins = [];

		if (!empty($this->_params['dosFrom'])) {
			$query->where($this->pixie->db->expr('DATE(case.time_start)'), '>=', \Opake\Helper\TimeFormat::formatToDB($this->_params['dosFrom']));
		}
		if (!empty($this->_params['dosTo'])) {
			$query->where($this->pixie->db->expr('DATE(case.time_start)'), '<=', \Opake\Helper\TimeFormat::formatToDB($this->_params['dosTo']));
		}

		if(!empty($this->_params['payer_type']) || !empty($this->_params['payer_name'])) {
			$query->join(['case_registration_insurance_types', 'crit'], ['case_registration.id', 'crit.registration_id']);
			if (!empty($this->_params['payer_type'])) {
				$query->where('crit.order', 1);
				$query->where('crit.deleted', 0);
				$query->where('crit.type', $this->_params['payer_type']);
			}
			if (!empty($this->_params['payer_name'])) {
				$query->join('insurance_data_regular', [['crit.insurance_data_id', 'insurance_data_regular.id'], ['crit.type', 'NOT IN', $this->pixie->db->arr([AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT, AbstractType::INSURANCE_TYPE_WORKERS_COMP, AbstractType::INSURANCE_TYPE_SELF_PAY, AbstractType::INSURANCE_TYPE_LOP ])]]);
				$query->join('insurance_data_auto_accident', [['crit.insurance_data_id', 'insurance_data_auto_accident.id'], ['crit.type', $this->pixie->db->expr(AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT)]]);
				$query->join('insurance_data_workers_comp', [['crit.insurance_data_id', 'insurance_data_workers_comp.id'], ['crit.type', $this->pixie->db->expr(AbstractType::INSURANCE_TYPE_WORKERS_COMP)]]);
				$query->where('crit.order', 1);
				$query->where('crit.deleted', 0);
				$model->query->where('and', [
					['or', ['insurance_data_regular.insurance_id', $this->_params['payer_name']]],
					['or', ['insurance_data_workers_comp.insurance_company_id', $this->_params['payer_name']]],
					['or', ['insurance_data_auto_accident.insurance_company_id', $this->_params['payer_name']]],
				]);
			}
		}

		if (!empty($this->_params['patientLastName'])) {
			$query->where('case_registration.last_name', 'like', '%' . $this->_params['patientLastName'] . '%');
		}
		if (!empty($this->_params['patientFirstName'])) {
			$query->where('case_registration.first_name', 'like', '%' . $this->_params['patientFirstName'] . '%');
		}
		if (!empty($this->_params['surgeon']) && !$user->isDoctor()) {
			$model->query->join('case_user', ['case.id', 'case_user.case_id'])
				->where('case_user.user_id', $this->_params['surgeon']);
		}
		if(!empty($this->_params['charge'])) {
			$chargesId = json_decode($this->_params['charge'], true);
			if (count($chargesId)) {
				$query->join('case_coding', ['case.id', 'case_coding.case_id']);
				$query->join('case_coding_bill', ['case_coding_bill.coding_id', 'case_coding.id']);
				$query->where('case_coding_bill.charge_master_entry_id', 'IN', $this->pixie->db->arr($chargesId));
			}
		}
		if (!empty($this->_params['billing_status'])) {
			$query->where($model->table . '.billing_status', $this->_params['billing_status']);
		}

		switch ($sort) {
			case 'dos':
				$model->order_by($model->table . '.time_start', $order);
				break;
			case 'mrn':
				$usedJoins[] = 'patient';
				$model->order_by('patient.mrn', $order);
				break;
			case 'patient_first_name':
				$usedJoins[] = 'patient';
				$model->order_by('patient.first_name', $order);
				break;
			case 'patient_last_name':
				$usedJoins[] = 'patient';
				$model->order_by('patient.last_name', $order);
				break;
			case 'account_id':
				$model->order_by($model->table . '.id', $order);
				break;
			case 'primary_payer_type':
				$subquery = 'SELECT
				    `id`, `registration_id`, `insurance_data_id`, `type`
				  FROM
				    `case_registration_insurance_types`
				  WHERE
				    `case_registration_insurance_types`.`order` = 1 AND `case_registration_insurance_types`.`deleted` = 0
				  ORDER BY
				    `case_registration_insurance_types`.`order` ASC';

				$model->query->join([$this->pixie->db->expr($subquery), 'insurance_type'], ['insurance_type.registration_id', 'case_registration.id']);
				$model->query->order_by($this->pixie->db->expr($this->getInsuranceTypeSql()), $order);
				break;
			case 'primary_payer_name':
				$subquery = 'SELECT
				    `id`, `registration_id`, `insurance_data_id`
				  FROM
				    `case_registration_insurance_types`
				  WHERE
				    `case_registration_insurance_types`.`order` = 1 AND `case_registration_insurance_types`.`deleted` = 0
				  ORDER BY
				    `case_registration_insurance_types`.`order` ASC';

				$model->query->join([$this->pixie->db->expr($subquery), 'insurance_type'], ['insurance_type.registration_id', 'case_registration.id']);
				$model->query->join('insurance_data_regular', ['insurance_type.insurance_data_id', 'insurance_data_regular.id']);
				$model->query->join('insurance_payor', ['insurance_data_regular.insurance_id', 'insurance_payor.id']);
				$model->order_by('insurance_payor.name', $order);
				break;

			case 'secondary_payer_type':
				$subquery = 'SELECT
				    `id`, `registration_id`, `insurance_data_id`, `type`
				  FROM
				    `case_registration_insurance_types`
				  WHERE
				    `case_registration_insurance_types`.`order` = 2 AND `case_registration_insurance_types`.`deleted` = 0
				  ORDER BY
				    `case_registration_insurance_types`.`order` ASC';

				$model->query->join([$this->pixie->db->expr($subquery), 'insurance_type'], ['insurance_type.registration_id', 'case_registration.id']);
				$model->query->order_by($this->pixie->db->expr($this->getInsuranceTypeSql()), $order);
				break;
			case 'secondary_payer_name':
				$subquery = 'SELECT
				    `id`, `registration_id`, `insurance_data_id`
				  FROM
				    `case_registration_insurance_types`
				  WHERE
				    `case_registration_insurance_types`.`order` = 2 AND `case_registration_insurance_types`.`deleted` = 0
				  ORDER BY
				    `case_registration_insurance_types`.`order` ASC';

				$model->query->join([$this->pixie->db->expr($subquery), 'insurance_type'], ['insurance_type.registration_id', 'case_registration.id']);
				$model->query->join('insurance_data_regular', ['insurance_type.insurance_data_id', 'insurance_data_regular.id']);
				$model->query->join('insurance_payor', ['insurance_data_regular.insurance_id', 'insurance_payor.id']);
				$model->order_by('insurance_payor.name', $order);
				break;
			case 'cpt':
				break;
			case 'provider':
				$model->query->join(['user', 'us'], ['us.id', $this->pixie->db->expr('(SELECT user_id FROM case_user WHERE case_user.case_id = case.id LIMIT 1)')]);
				$model->order_by('us.first_name', $order)->order_by('us.last_name', $order);
				break;
			case 'writeoff':
				$model->query->fields(
					$this->pixie->db->expr('
						SQL_CALC_FOUND_ROWS `'. $model->table .'`.*, 
						SUM( CASE WHEN billing_ledger_payment_info.payment_source = "'. PaymentInfo::PAYMENT_SOURCE_WRITE_OFF .'" THEN  applied_payment.amount ELSE 0 END) AS writeoff'
					)
				);
				$usedJoins[] = 'billing_ledger_payment_info';
				$model->order_by($this->pixie->db->expr('writeoff'), $order);
				break;
			case 'charges':
				$model->query->fields(
					$this->pixie->db->expr('
						SQL_CALC_FOUND_ROWS `'. $model->table .'`.*, 
						SUM(case_coding_bill.charge) AS charges'
					)
				);
				$model->query->join('case_coding', ['case.id', 'case_coding.case_id']);
				$model->query->join('case_coding_bill', ['case_coding_bill.coding_id', 'case_coding.id'], 'left');
				$model->order_by($this->pixie->db->expr('charges'), $order);
				break;
			case 'payment':
				$model->query->fields(
					$this->pixie->db->expr('
						SQL_CALC_FOUND_ROWS `'. $model->table .'`.*, 
						SUM(applied_payment.amount) AS payment'
					)
				);
				$usedJoins[] = 'applied_payment';
				$model->order_by($this->pixie->db->expr('payment'), $order);
				break;
			case 'adjustments':
				$model->query->fields(
					$this->pixie->db->expr('
						SQL_CALC_FOUND_ROWS `'. $model->table .'`.*, 
						SUM( CASE WHEN billing_ledger_payment_info.payment_source = "'. PaymentInfo::PAYMENT_SOURCE_ADJUSTMENT .'" THEN  applied_payment.amount ELSE 0 END) AS adjustments'
					)
				);
				$usedJoins[] = 'billing_ledger_payment_info';
				$model->order_by($this->pixie->db->expr('adjustments'), $order);
				break;
			case 'balance':
				$model->query->fields(
					$this->pixie->db->expr('
						SQL_CALC_FOUND_ROWS `'. $model->table .'`.*, 
						(SUM(case_coding_bill.amount) - SUM(IFNULL(applied_payment.amount, 0))) as balance'
					)
				);
				$usedJoins[] = 'applied_payment';
				$model->order_by($this->pixie->db->expr('balance'), $order);
				break;

		}

		if (in_array('patient', $usedJoins)) {
			$model->query->join('patient', ['case_registration.patient_id', 'patient.id']);
		}

		if(in_array('applied_payment', $usedJoins) || in_array('billing_ledger_payment_info', $usedJoins)) {
			$model->query->join('case_coding', ['case.id', 'case_coding.case_id']);
			$model->query->join('case_coding_bill', ['case_coding_bill.coding_id', 'case_coding.id'], 'left');
			$model->query->join(['billing_ledger_applied_payment', 'applied_payment'], ['case_coding_bill.id', 'applied_payment.coding_bill_id'], 'left');
			if(in_array('billing_ledger_payment_info', $usedJoins)) {
				$model->query->join('billing_ledger_payment_info', ['billing_ledger_payment_info.id', 'applied_payment.payment_info_id'], 'left');
			}
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

	protected function getInsuranceTypeSql()
	{
		$sql = 'CASE `insurance_type`.`type` ';
		$types = AbstractType::getInsuranceTypesList();
		foreach ($types as $id => $desc) {
			$sql .= 'WHEN ' . $id . ' THEN \'' . $desc . '\' ';
		}
		$sql .= 'ELSE 1 END';

		return $sql;
	}
}