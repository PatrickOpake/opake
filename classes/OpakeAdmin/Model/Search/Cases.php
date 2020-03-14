<?php

namespace OpakeAdmin\Model\Search;

use Opake\Model\Cases\OperativeReport;
use Opake\Model\Profession;
use Opake\Model\Search\AbstractSearch;

class Cases extends AbstractSearch
{

	public function search($model, $request)
	{

		$model = parent::prepare($model, $request);

		$this->_params = [
			'id' => trim($request->get('id')),
			'start' => trim($request->get('start')),
			'end' => trim($request->get('end')),
			'end_exclude' => trim($request->get('end_exclude')),
			'dos' => trim($request->get('dos')),
			'user' => trim($request->get('user')),
			'site' => trim($request->get('site')),
			'location' => trim($request->get('location')),
			'stage' => trim($request->get('stage')),
			'phase' => trim($request->get('phase')),
			'procedure' => trim($request->get('procedure')),
			'doctor' => trim($request->get('doctor')),
			'patient_name' => trim($request->get('patient_name')),
			'patient' => trim($request->get('patient')),
			'view_type' => trim($request->get('view_type')),
			'start_of_week' => trim($request->get('start_of_week')),
			'end_of_week' => trim($request->get('end_of_week')),
			'cancel_date' => trim($request->get('cancel_date')),
			'reschedule_date' => trim($request->get('reschedule_date')),
			'patient_first_name' => trim($request->get('patient_first_name')),
			'patient_last_name' => trim($request->get('patient_last_name')),
			'mrn' => trim($request->get('mrn')),
			'cancel_status' => trim($request->get('cancel_status')),
			'cancelled_user' => trim($request->get('cancelled_user')),
			'verification_status' => trim($request->get('verification_status')),
			'hidden_surgeon_ids' => trim($request->get('hidden_surgeon_ids')),
			'non_surgeon' => trim($request->get('non_surgeon')),
			'user_id' => trim($request->get('user_id')),
			'inventory_type' => trim($request->get('inventory_type')),
			'inventory_manf' => trim($request->get('inventory_manf')),
			'inventory_desc' => trim($request->get('inventory_desc')),
			'inventory_id' => trim($request->get('inventory_id')),
			'alert' => trim($request->get('alert')),
		];

		$sort = $request->get('sort_by', 'date');
		$order = $request->get('sort_order', 'DESC');

		$user = $this->pixie->auth->user();

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->group_by('case.id');

		$usedJoins = [];

		if ($this->pixie->permissions->getAccessLevel('cases', 'view')->isSelfAllowed()) {

			$usedJoins[] = 'users';

			$usePracticeGroups = false;
			$userId = $user->id();

			if ($user->isSatelliteOffice()) {
				$userPracticeGroupIds = $user->getPracticeGroupIds();
				if ($userPracticeGroupIds) {
					$usedJoins[] = 'practice_groups';
					$model->where('user.organization_id', $user->organization_id);
					$model->where('and', [
						['or', ['upg.practice_group_id', 'IN', $this->pixie->db->expr("(" . implode(',', $userPracticeGroupIds) . ")")]],
						['or', ['case_user.user_id', $userId]]
					]);

					$usePracticeGroups = true;
				}
			}

			if (!$usePracticeGroups && !$this->_params['non_surgeon']) {
				$model->where('and', [
					['or', ['case_user.user_id', $userId]],
					['or', ['case_other_staff.staff_id', $userId]],
					['or', ['case_surgeon_assistant.surgeon_assistant_id', $userId]],
					['or', ['case_co_surgeon.co_surgeon_id', $userId]],
					['or', ['case_supervising_surgeon.supervising_surgeon_id', $userId]],
					['or', ['case_first_assistant_surgeon.assistant_surgeon_id', $userId]],
					['or', ['case_assistant.assistant_id', $userId]],
					['or', ['case_anesthesiologist.anesthesiologist_id', $userId]],
					['or', ['case_dictated_by.dictated_by_id', $userId]],
				]);
			}

		}

		if ($this->_params['non_surgeon']) {

			$usedJoins[] = 'users';

			$user_id = $user->id();
			if($this->_params['user_id']) {
				$user_id = $this->_params['user_id'];
			}

			$model->where('and', [
				['or', ['case_user.user_id', $user_id]],
				['or', ['case_surgeon_assistant.surgeon_assistant_id', $user_id]],
				['or', ['case_other_staff.staff_id', $user_id]],
				['or', ['case_co_surgeon.co_surgeon_id', $user_id]],
				['or', ['case_supervising_surgeon.supervising_surgeon_id', $user_id]],
				['or', ['case_first_assistant_surgeon.assistant_surgeon_id', $user_id]],
				['or', ['case_assistant.assistant_id', $user_id]],
				['or', ['case_anesthesiologist.anesthesiologist_id', $user_id]],
				['or', ['case_dictated_by.dictated_by_id', $user_id]],
			]);
		}

		if($this->_params['alert']) {
			$params = json_decode($this->_params['alert'], true);
			if(!empty($params['not_insurance_verified'])) {
				$usedJoins[] = 'registration';
				$model->where(
					'case_registration.insurance_verified', 0);
			}
			if(!empty($params['not_completed_preauthorized'])) {
				$usedJoins[] = 'registration';
				$model->where(
					'case_registration.is_pre_authorization_completed', 0);
			}
			if(!empty($params['has_pre_certification_required'])) {
				$usedJoins[] = 'registration';
				$model->where(
					'case_registration.pre_certification_required', 1);
			}
			if(!empty($params['has_not_been_pre_certified'])) {
				$usedJoins[] = 'registration';
				$model->where(
					'case_registration.pre_certification_required', 1);
				$model->where(
					'case_registration.pre_certification_obtained', 0);
			}
			if(!empty($params['is_self_funded'])) {
				$usedJoins[] = 'registration';
				$model->where(
					'case_registration.self_funded', 1);
			}
			if(!empty($params['has_oon_benefits'])) {
				$usedJoins[] = 'registration';
				$model->where(
					'case_registration.is_oon_benefits_cap', 1);
			}
			if(!empty($params['has_asc_benefits'])) {
				$usedJoins[] = 'registration';
				$model->where(
					'case_registration.is_asc_benefits_cap', 1);
			}
			if(!empty($params['has_clauses_under_medicare_entitlement'])) {
				$usedJoins[] = 'registration';
				$model->where(
					'case_registration.is_clauses_pertaining', 1);
			}
			if(!empty($params['has_clauses_under_patient_policy'])) {
				$usedJoins[] = 'registration';
				$model->where(
					'case_registration.is_pre_existing_clauses', 1);
			}
			if(!empty($params['cases_report_completed_48hrs_case_end'])) {
				$openStatuses = [
					\Opake\Model\Cases\OperativeReport::STATUS_OPEN,
					\Opake\Model\Cases\OperativeReport::STATUS_DRAFT
				];
				$model->query->join('case_op_report', [$model->table . '.id', 'case_op_report.case_id']);
				$model->where([
					['case_op_report.surgeon_id', $this->pixie->db->expr("(SELECT user_id FROM case_user WHERE case_user.case_id = " . $model->table . ".id LIMIT 1)")],
					['case_op_report.type', OperativeReport::TYPE_SURGEON],
					['case_op_report.status', 'IN', $this->pixie->db->arr($openStatuses)]
				]);
				$model->where($this->pixie->db->expr('now()'), '>', $this->pixie->db->expr('date_add(case.time_start, INTERVAL 48 hour)'));
			}
		}

		if ($this->_params['user'] !== '') {

			$usedJoins[] = 'users';

			$model->where('and', [
				['or', [$this->pixie->db->expr("CONCAT_WS(' ',user.first_name,user.last_name)"), 'like', '%' . $this->_params['user'] . '%']],
				['or', ['user.email', 'like', '%' . $this->_params['user'] . '%']]
			]);
		}

		if ($this->_params['location'] !== '' || $this->_params['site'] !== '' || $sort === 'location') {
			$usedJoins[] = 'location';
		}

		if ($this->_params['location'] !== '') {
			$model->where('location.name', 'like', '%' . $this->_params['location'] . '%');
		}

		if ($this->_params['site'] !== '') {
			$usedJoins[] = 'site';
			$model->where('site.name', 'like', '%' . $this->_params['site'] . '%');
		}

		if ($this->_params['start'] !== '') {
			$model->where($this->pixie->db->expr('DATE(case.time_start)'), '>=', $this->_params['start']);
		}

		if ($this->_params['end'] !== '') {
			$model->where($this->pixie->db->expr('DATE(case.time_start)'), '<=', $this->_params['end']);
		}

		if ($this->_params['end_exclude'] !== '') {
			$model->where($this->pixie->db->expr('DATE(case.time_start)'), '<', $this->_params['end_exclude']);
		}

		if ($this->_params['dos'] !== '') {
			if ($this->_params['view_type'] == 'week') {
				$model->where($this->pixie->db->expr('DATE(case.time_start)'), '>=', \Opake\Helper\TimeFormat::formatToDB($this->_params['start_of_week']))
					->where($this->pixie->db->expr('DATE(case.time_start)'), '<=', \Opake\Helper\TimeFormat::formatToDB($this->_params['end_of_week']))
					->order_by('case.time_start', 'ASC');
			} else {
				$model->where($this->pixie->db->expr('DATE(case.time_start)'), \Opake\Helper\TimeFormat::formatToDB($this->_params['dos']))
					->order_by('case.time_start', 'ASC');
			}
		}

		if ($this->_params['cancel_date'] !== '') {
				$model->where($this->pixie->db->expr('DATE(case.cancel_time)'), \Opake\Helper\TimeFormat::formatToDB($this->_params['cancel_date']))
					->order_by('case.cancel_time', 'ASC');
		}

		if ($this->_params['stage'] !== '') {
			$model->where($model->table . '.stage', $this->_params['stage']);
		}

		if ($this->_params['phase'] !== '') {
			$model->where($model->table . '.phase', $this->_params['phase']);
		}

		if ($this->_params['procedure'] !== '') {
			$model->where($model->table . '.type_id', $this->_params['procedure']);
		}

		if ($this->_params['doctor'] !== '') {
			$usedJoins[] = 'users';
			$model->where('case_user' . '.user_id', $this->_params['doctor']);
		}

		if ($this->_params['hidden_surgeon_ids'] !== '') {
			$surgeonsIds = json_decode($this->_params['hidden_surgeon_ids'], true);

			if (count($surgeonsIds)) {
				$model->query->join(['case_user', 'fcu'], ['fcu.user_id',
					$this->pixie->db->expr("(SELECT user_id FROM case_user WHERE case_user.case_id = case.id LIMIT 1)")
				])
					->where('fcu' . '.user_id', 'NOT IN', $this->pixie->db->expr("(" . implode(',', $surgeonsIds) . ")"));
			}
		}

		if ($this->_params['id'] !== '') {
			$model->where($model->table . '.id', $this->_params['id']);
		}

		if ($this->_params['patient'] !== '') {
			$usedJoins[] = 'registration';
			$model->query->where('case_registration.patient_id', $this->_params['patient']);
		}

		if ($this->_params['patient_name'] !== '') {
			$usedJoins[] = 'patient';
			$model->query->where($this->pixie->db->expr("CONCAT(patient.last_name, ' ', patient.first_name)"), 'like', '%' . $this->_params['patient_name'] . '%');
		}

		if (($this->_params['patient_first_name'] !== '') || ($this->_params['patient_last_name'] !== '')) {
			$usedJoins[] = 'patient';
			$model->query->where('patient.first_name', 'like', '%' . $this->_params['patient_first_name'] . '%')
				->where('patient.last_name', 'like', '%' . $this->_params['patient_last_name'] . '%');
		}

		if ($this->_params['mrn'] !== '') {
			$usedJoins[] = 'patient';
			$model->query->where($this->pixie->db->expr("CONCAT(LPAD(patient.mrn, 5, '0'), '-', patient.mrn_year)"), 'like', '%' . $this->_params['mrn'] . '%');
		}

		if ($this->_params['cancel_status'] !== '') {
			$model->where($model->table . '.cancel_status', $this->_params['cancel_status']);
		}

		if ($this->_params['verification_status'] !== '') {
			$usedJoins[] = 'registration';
			$model->where('case_registration.verification_status', $this->_params['verification_status']);
		}


		if($this->_params['inventory_type'] !== '' ||  $this->_params['inventory_manf'] !== ''
			|| $this->_params['inventory_desc'] !== '' || $this->_params['inventory_id'] !== '') {

			$model->query->join('card_staff', [$model->table . '.id', 'card_staff.case_id']);
			$model->query->join('card_staff_item', ['card_staff_item.card_id', 'card_staff.id']);
			$model->query->join('inventory', ['card_staff_item.inventory_id', 'inventory.id']);

			if($this->_params['inventory_type'] !== '') {
				$model->where('inventory.type', 'like', '%' . $this->_params['inventory_type'] . '%');
			}

			if($this->_params['inventory_manf'] !== '') {
				$model->query->join('vendor', ['inventory.manf_id', 'vendor.id']);
				$model->where('vendor.name', 'like', '%' . $this->_params['inventory_manf'] . '%');
			}

			if($this->_params['inventory_desc'] !== '') {
				$model->where('inventory.desc', 'like', '%' . $this->_params['inventory_desc'] . '%');
			}

			if($this->_params['inventory_id'] !== '') {
				$model->where('inventory.id',  $this->_params['inventory_id']);
			}
		}

		switch ($sort) {
			case 'id':
				$model->order_by($model->table . '.id', $order);
				break;
			case 'date':
				$model->order_by($model->table . '.time_start', $order);
				break;
			case 'location':
				$usedJoins[] = 'location';
				$model->order_by('location.name', $order);
				break;
			case 'stage':
				$model->order_by($model->table . '.stage', $order);
				break;
			case 'phase':
				$phasesCase = [];
				foreach (\Opake\Model\Cases\Item::getStagePhases() as $stage => $phases) {
					$phasesCase += $phases;
				}
				$model->order_by($this->pixie->db->expr('case ' . $model->table . '.phase ' . $this->caseSql($phasesCase) . ' end'), $order);
				break;
			case 'patient_name':
				$usedJoins[] = 'patient';
				$model->order_by('patient.last_name', $order)
					->order_by('patient.first_name', $order)
					->order_by($model->table . '.time_start', $order);
				break;
			case 'mrn':
				$usedJoins[] = 'patient';
				$model->order_by('patient.mrn', $order)
					->order_by($model->table . '.time_start', $order);
				break;
			case 'cancel_date':
				$model->order_by($model->table . '.cancel_time', $order);
				break;
			case 'start':
				$model->order_by($model->table . '.time_start', $order);
				break;
		}

		if (in_array('users', $usedJoins)) {
			$model->query->join('case_user', [$model->table . '.id', 'case_user.case_id']);
			$model->query->join('user', ['case_user.user_id', 'user.id']);

			$model->query->join('case_other_staff', ['case.id', 'case_other_staff.case_id'], 'left');
			$model->query->join('case_co_surgeon', ['case.id', 'case_co_surgeon.case_id'], 'left');
			$model->query->join('case_surgeon_assistant', ['case.id', 'case_surgeon_assistant.case_id'], 'left');
			$model->query->join('case_supervising_surgeon', ['case.id', 'case_supervising_surgeon.case_id'], 'left');
			$model->query->join('case_first_assistant_surgeon', ['case.id', 'case_first_assistant_surgeon.case_id'], 'left');
			$model->query->join('case_assistant', ['case.id', 'case_assistant.case_id'], 'left');
			$model->query->join('case_anesthesiologist', ['case.id', 'case_anesthesiologist.case_id'], 'left');
			$model->query->join('case_dictated_by', ['case.id', 'case_dictated_by.case_id'], 'left');

			if (in_array('practice_groups', $usedJoins)) {
				$model->query->join(['user_practice_groups', 'upg'], ['case_user.user_id', 'upg.user_id'], 'left');
			}
		}

		if (in_array('location', $usedJoins)) {
			$model->query->join('location', [$model->table . '.location_id', 'location.id']);
			if (in_array('site', $usedJoins)) {
				$model->query->join('site', ['location.site_id', 'site.id']);
			}
		}

		if (in_array('patient', $usedJoins) || in_array('registration', $usedJoins)) {
			$model->query->join('case_registration', [$model->table . '.id', 'case_registration.case_id']);
			if (in_array('patient', $usedJoins)) {
				$model->query->join('patient', ['case_registration.patient_id', 'patient.id']);
			}
		}

		if ($this->_pagination) {
			$model->pagination($this->_pagination);
		}

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
