<?php

namespace OpakeAdmin\Controller\Analytics\Credentials;

use Opake\Model\Profession;
use OpakeAdmin\Helper\Export\UserCredentialsExport;

class Ajax extends \OpakeAdmin\Controller\Ajax
{
	public function before() {
		parent::before();
		$this->iniOrganization($this->request->param('id'));
	}

	public function actionSearch()
	{
		$model = $this->orm->get('User_Credentials');

		$query = $model->query;
		$query->fields('user_credentials.*');

		$query->join('user', ['user.id', 'user_credentials.user_id'], 'inner')
			->where('user.organization_id', $this->org->id);

		$user = $this->logged();
		if ($user->isSatelliteOffice()) {
			$userPracticeGroupIds = $user->getPracticeGroupIds();
			if ($userPracticeGroupIds) {
				$query->join(['user_practice_groups', 'upg'], ['user_credentials.user_id', 'upg.user_id'], 'left')
				->where('and', [
					['or', ['upg.practice_group_id', 'IN', $this->pixie->db->arr($userPracticeGroupIds)]],
					['or', ['user_credentials.user_id', $user->id()]]
				])
				->group_by('upg.practice_group_id');
			}
		}

		$search = new \OpakeAdmin\Model\Search\Analytics\Credentials($this->pixie);
		$results = $search->search($model, $this->request);

		$items = [];
		foreach ($results as $result) {
			$items[] = $result->toShortArray();
		}

		$this->result = [
			'items' => $items,
			'total_count' => $search->getPagination()->getCount(),
			'staffs_with_approaching_dates_count' => (int) $search->getStaffsWithApproachingDatesCount($model),
			'expired_count' => [
				'medical_licence_exp_date' => (int) $search->getMedicalLicenceExpDateCount($model),
				'dea_exp_date' => (int) $search->getDeaExpDateCount($model),
				'cds_exp_date' => (int) $search->getCdsExpDateCount($model),
				'insurance_exp_date' => (int) $search->getInsuranceExpDateCount($model),
				'insurance_reappointment_date' => (int) $search->getInsuranceReappointmentDateCount($model),
				'acls_date' => (int) $search->getAclsDateCount($model),
				'immunizations_ppp_due' => (int) $search->getPppDueDateCount($model),
				'immunizations_help_b' => (int) $search->getHelpbDateCount($model),
				'immunizations_rubella' => (int) $search->getRubellaDateCount($model),
				'immunizations_rubeola' => (int) $search->getRubeolaDateCount($model),
				'immunizations_varicela' => (int) $search->getVaricelaDateCount($model),
				'immunizations_mumps' => (int) $search->getMumpsDateCount($model),
				'immunizations_flue' => (int) $search->getFlueDateCount($model),
				'retest_date' => (int) $search->getRetestDateCount($model),
				'licence_expr_date' => (int) $search->getLicenceExpDateCount($model),
				'bls_date' => (int) $search->getBlsDateCount($model),
				'cnor_date' => (int) $search->getCnorDateCount($model),
				'malpractice_exp_date' => (int) $search->getMalpracticeExpDateCount($model),
				'hp_exp_date' => (int) $search->getHpExpDateCount($model)
			]
		];
	}

	public function actionExportMedicalStaffs()
	{
		$model = $this->orm->get('User_Credentials');

		$query = $model->query;
		$query->fields('user_credentials.*');

		$query->join('user', ['user.id', 'user_credentials.user_id'], 'inner')
			->where('user.organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Analytics\Credentials($this->pixie, false);
		$results = $search->search($model, $this->request);
		
		$export = new UserCredentialsExport($this->pixie);
		$csv = $export->generateCsvForMedicalStaffs($results);

		$this->result = [
			'success' => true,
			'url' => $csv->getWebPath()
		];
	}

	public function actionExportNonSurgicalStaffs()
	{
		$model = $this->orm->get('User_Credentials');

		$query = $model->query;
		$query->fields('user_credentials.*');

		$query->join('user', ['user.id', 'user_credentials.user_id'], 'inner')
			->where('user.organization_id', $this->org->id);

		$search = new \OpakeAdmin\Model\Search\Analytics\Credentials($this->pixie, false);
		$results = $search->search($model, $this->request);

		$export = new UserCredentialsExport($this->pixie);
		$csv = $export->generateCsvForNonSurgicalStaffs($results);

		$this->result = [
			'success' => true,
			'url' => $csv->getWebPath()
		];
	}
}