<?php

namespace OpakeAdmin\Model\Search\Cases;

use Opake\Model\Insurance\AbstractType;
use Opake\Model\Search\AbstractSearch;

class Billing extends AbstractSearch {

	/**
	 * Params
	 * @var array
	 */
	protected $isCompleted = true;

	public function __construct($pixie, $isCompleted) {
		$this->isCompleted = $isCompleted;
		parent::__construct($pixie);
	}

	public function search($model, $request) {

		$model = parent::prepare($model, $request);

		$this->_params = [
		    'id' => trim($request->get('id')),
		    'dos' => trim($request->get('dos')),
		    'dosFrom' => trim($request->get('dosFrom')),
		    'dosTo' => trim($request->get('dosTo')),
		    'patient_first_name' => trim($request->get('patient_first_name')),
		    'patient_last_name' => trim($request->get('patient_last_name')),
		    'provider' => trim($request->get('provider')),
		    'status' => trim($request->get('status')),
		    'insurances' => trim($request->get('insurances')),
		];

		$sort = $request->get('sort_by', 'dos');
		$order = $request->get('sort_order', 'DESC');

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->join(['case_claim', 'claim'], [$model->table . '.id', 'claim.case_id'])
			->join(['case_registration', 'r'], [$model->table . '.id', 'r.case_id'])
			->join(['case_coding', 'c'], [$model->table . '.id', 'c.case_id'])
			->group_by('case.id');

		if ($this->isCompleted) {
			$model->query
				->where('status', \Opake\Model\Cases\Item::APPOINTMENT_STATUS_COMPLETED)
				->where('claim.id', 'IS NOT NULL', $this->pixie->db->expr(''));
		} else {
			$model->where('status', '!=',\Opake\Model\Cases\Item::APPOINTMENT_STATUS_COMPLETED);
		}

		if ($this->_params['id'] !== '') {
			$model->where($model->table . '.id', $this->_params['id']);
		}
		if ($this->_params['dos'] !== '') {
			$model->where($this->pixie->db->expr('DATE(case.time_start)'),  \Opake\Helper\TimeFormat::formatToDB($this->_params['dos']));
		}
		if ($this->_params['dosFrom'] !== '') {
			$model->where($this->pixie->db->expr('DATE(case.time_start)'), '>=', \Opake\Helper\TimeFormat::formatToDB($this->_params['dosFrom']));
		}
		if ($this->_params['dosTo'] !== '') {
			$model->where($this->pixie->db->expr('DATE(case.time_start)'), '<=', \Opake\Helper\TimeFormat::formatToDB($this->_params['dosTo']));
		}
		if ($this->_params['patient_first_name'] !== '' || $this->_params['patient_last_name'] !== '') {
			if ($this->_params['patient_first_name'] !== '') {
				$model->where('r.first_name', 'like', '%' . $this->_params['patient_first_name'] . '%');
			}
			if ($this->_params['patient_last_name'] !== '') {
				$model->where('r.last_name', 'like', '%' . $this->_params['patient_last_name'] . '%');
			}
		}

		if($this->_params['status'] !== '') {
			if($this->_params['status'] == \Opake\Model\Cases\Item::BILLING_STATUS_BEGIN) {
				$model->query->join(['billing_navicure_claim', 'bnc'], [$model->table . '.id', 'bnc.case_id']);
				$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*, count(bnc.id) as bnc_count'));
				$model->where([
					['c.case_id', 'IS NULL', $this->pixie->db->expr('')],
					['and', ['r.verification_status', '!=', 1]]
				]);
				$model->query->having($this->pixie->db->expr('bnc_count'), 0);
			} else if($this->_params['status'] == \Opake\Model\Cases\Item::BILLING_STATUS_CONTINUE) {
				$model->query->join(['billing_navicure_claim', 'bnc'], [$model->table . '.id', 'bnc.case_id']);
				$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*, count(bnc.id) as bnc_count'));
				$model->where([
					['c.case_id', 'IS NOT NULL', $this->pixie->db->expr('')],
					['or', ['r.verification_status', 1]]
				]);
				$model->query->having($this->pixie->db->expr('bnc_count'), 0);
			} else if($this->_params['status'] == \Opake\Model\Cases\Item::BILLING_STATUS_COMPLETE) {
				$model->query->join(['billing_navicure_claim', 'bnc'], [$model->table . '.id', 'bnc.case_id']);
				$model->where([
					['c.is_ready_professional_claim',  0],
					['and', ['c.is_ready_institutional_claim',  0]],
				]);
				$model->query->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*, count(bnc.id) as bnc_count'));
				$model->query->having($this->pixie->db->expr('bnc_count'), '>', 0);
			} else if($this->_params['status'] == \Opake\Model\Cases\Item::BILLING_STATUS_READY) {
				$model->where([
					['c.is_ready_professional_claim',  1],
					['or', ['c.is_ready_institutional_claim',  1]],
				]);
			}
		}

		if ($this->_params['insurances'] !== '') {
			$insurancesIds = json_decode($this->_params['insurances'], true);
			if (count($insurancesIds)) {
				$insuranceDataSearchParams = [];
				foreach ($insurancesIds as $item) {
					if(is_array($item) && isset($item['isCustomAdded']) && $item['isCustomAdded']) {
						$insuranceDataSearchParams[] = $item['name'];
					} else {
						$insuranceDataSearchParams[] = $item['id'];
					}
				}

				$model->query->join(['case_registration_insurance_types', 'crit'], ['crit.registration_id', 'r.id'], 'inner');
				$model->query->join('insurance_data_regular', [['crit.insurance_data_id', 'insurance_data_regular.id'], ['crit.type', 'NOT IN', $this->pixie->db->arr([AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT, AbstractType::INSURANCE_TYPE_WORKERS_COMP, AbstractType::INSURANCE_TYPE_SELF_PAY, AbstractType::INSURANCE_TYPE_LOP ])]]);
				$model->query->join('insurance_data_auto_accident', [['crit.insurance_data_id', 'insurance_data_auto_accident.id'], ['crit.type', $this->pixie->db->expr(AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT)]]);
				$model->query->join('insurance_data_workers_comp', [['crit.insurance_data_id', 'insurance_data_workers_comp.id'], ['crit.type', $this->pixie->db->expr(AbstractType::INSURANCE_TYPE_WORKERS_COMP)]]);
				$model->query->join('insurance_data_description', [['crit.insurance_data_id', 'insurance_data_description.id'], ['crit.type', 'IN', $this->pixie->db->arr([AbstractType::INSURANCE_TYPE_SELF_PAY, AbstractType::INSURANCE_TYPE_LOP])]]);
				$model->query->where('crit.order', 'IS NOT NULL', $this->pixie->db->expr(''));
				$model->query->where('c.id', 'IS NOT NULL', $this->pixie->db->expr(''));
				$model->query->where('crit.deleted', 0);
				$model->query->where('and', [
					['or', ['insurance_data_regular.insurance_id', 'IN', $this->pixie->db->arr($insuranceDataSearchParams)]],
					['or', ['insurance_data_workers_comp.insurance_company_id', 'IN', $this->pixie->db->arr($insuranceDataSearchParams)]],
					['or', ['insurance_data_auto_accident.insurance_company_id', 'IN', $this->pixie->db->arr($insuranceDataSearchParams)]],
					['or', ['insurance_data_description.description', 'IN', $this->pixie->db->arr($insuranceDataSearchParams)]],
				]);
			}
		}

		$this->pixie->db->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
			->execute()->get('count');

		if ($this->_params['provider'] !== '') {
			$model->query->join('case_user', [$model->table . '.id', 'case_user.case_id'])
					->where('case_user.user_id', $this->_params['provider']);
		}

		switch ($sort) {
			case 'id':
				$model->order_by($model->table . '.id', $order);
				break;
			case 'patient':
				$model->order_by('r.first_name', $order)->order_by('r.last_name', $order);
				break;
			case 'dos':
				$model->order_by($model->table . '.time_start', $order);
				break;
		}

		$results = $model->pagination($this->_pagination)->find_all()->as_array();

		$count = $this->pixie->db
				->query('select')->fields($this->pixie->db->expr('FOUND_ROWS() as count'))
				->execute()->get('count');

		$this->_pagination->setCount($count);

		return $results;
	}
}