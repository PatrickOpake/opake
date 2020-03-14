<?php

namespace OpakeAdmin\Model\Search\Analytics;

use Opake\Model\Profession;
use Opake\Model\Search\AbstractSearch;

class Credentials extends AbstractSearch
{
	protected $medicalProfessionsIds = [Profession::SURGEON, Profession::ANESTHESIOLOGIST, Profession::PHYSICIAN_ASSISTANT,
		Profession::NURSE_ANESTHETIST, Profession::NURSE_PRACTITIONER];

	public function search($model, $request)
	{
		$model = parent::prepare($model, $request);

		$this->_params = [
			'id' => trim($request->get('id')),
			'type' => trim($request->get('type')),
			'user' => trim($request->get('user')),
			'user_name' => trim($request->get('user_name')),
			'with_expired_dates' => trim($request->get('with_expired_dates'))
		];

		$sort = $request->get('sort_by', 'id');
		$order = $request->get('sort_order', 'DESC');

		$model->query
			->fields($this->pixie->db->expr('SQL_CALC_FOUND_ROWS `' . $model->table . '`.*'))
			->group_by($model->table . '.id');

		if ($this->_params['user'] !== '') {
			$model->where($model->table . '.user_id', $this->_params['user']);
		}
		if ($this->_params['type'] !== '') {
			if ($this->_params['type'] == 'medical') {
				$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));

				if ($this->_params['with_expired_dates'] == 'true') {
					$model->where('and', [
						['or', [$this->pixie->db->expr('DATE(user_credentials.medical_licence_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.dea_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.cds_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.insurance_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.insurance_reappointment_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.acls_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_ppp_due)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_help_b)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_rubella)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_rubeola)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_varicela)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_mumps)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_flue)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.retest_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
					]);
				}
			}
			if ($this->_params['type'] == 'non-surgical') {
				$model->where('user.profession_id', 'NOT IN', $this->pixie->db->arr($this->medicalProfessionsIds));

				if ($this->_params['with_expired_dates'] == 'true') {
					$model->where('and', [
						['or', [$this->pixie->db->expr('DATE(user_credentials.licence_expr_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.bls_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.cnor_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.malpractice_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.hp_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.acls_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_ppp_due)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_help_b)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_rubella)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_rubeola)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_varicela)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_mumps)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
						['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_flue)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime())]],
					]);
				}
			}
		}

		switch ($sort) {
			case 'user_name':
				$model->order_by('user.last_name', $order)
					->order_by('user.first_name', $order);
				break;
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

	public function getStaffsWithApproachingDatesCount($model)
	{
		if ($this->_params['type'] == 'medical') {
			$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where('and', [
				['or', [$this->pixie->db->expr('DATE(user_credentials.medical_licence_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.dea_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.cds_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.insurance_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.insurance_reappointment_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.acls_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_ppp_due)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_help_b)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_rubella)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_rubeola)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_varicela)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_mumps)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_flue)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.retest_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
			]);

			return $model->count_all();
		} else {
			$model->where('user.profession_id', 'NOT IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where('and', [
				['or', [$this->pixie->db->expr('DATE(user_credentials.licence_expr_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.bls_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.cnor_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.malpractice_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.hp_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.acls_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_ppp_due)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_help_b)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_rubella)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_rubeola)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_varicela)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_mumps)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
				['or', [$this->pixie->db->expr('DATE(user_credentials.immunizations_flue)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime("+90 day"))]],
			]);

			return $model->count_all();
		}
	}

	public function getMedicalLicenceExpDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'medical') {
			$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.medical_licence_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			return 0;
		}
	}

	public function getDeaExpDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'medical') {
			$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.dea_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			return 0;
		}
	}

	public function getCdsExpDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'medical') {
			$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.cds_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			return 0;
		}
	}

	public function getInsuranceExpDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'medical') {
			$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.insurance_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			return 0;
		}
	}

	public function getInsuranceReappointmentDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'medical') {
			$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.insurance_reappointment_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			return 0;
		}
	}

	public function getAclsDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'medical') {
			$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.acls_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			$model->where('user.profession_id', 'NOT IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.acls_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		}
	}

	public function getPppDueDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'medical') {
			$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.immunizations_ppp_due)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			$model->where('user.profession_id', 'NOT IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.immunizations_ppp_due)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		}
	}

	public function getHelpbDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'medical') {
			$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.immunizations_help_b)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			$model->where('user.profession_id', 'NOT IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.immunizations_help_b)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		}
	}

	public function getRubellaDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'medical') {
			$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.immunizations_rubella)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			$model->where('user.profession_id', 'NOT IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.immunizations_rubella)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		}
	}

	public function getRubeolaDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'medical') {
			$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.immunizations_rubeola)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			$model->where('user.profession_id', 'NOT IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.immunizations_rubeola)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		}
	}

	public function getVaricelaDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'medical') {
			$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.immunizations_varicela)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			$model->where('user.profession_id', 'NOT IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.immunizations_varicela)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		}
	}

	public function getMumpsDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'medical') {
			$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.immunizations_mumps)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			$model->where('user.profession_id', 'NOT IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.immunizations_mumps)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		}
	}

	public function getFlueDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'medical') {
			$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.immunizations_flue)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			$model->where('user.profession_id', 'NOT IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.immunizations_flue)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		}
	}

	public function getRetestDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'medical') {
			$model->where('user.profession_id', 'IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.retest_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			return 0;
		}
	}

	public function getLicenceExpDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'non-surgical') {
			$model->where('user.profession_id', 'NOT IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.licence_expr_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			return 0;
		}
	}

	public function getBlsDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'non-surgical') {
			$model->where('user.profession_id', 'NOT IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.bls_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			return 0;
		}
	}

	public function getCnorDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'non-surgical') {
			$model->where('user.profession_id', 'NOT IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.cnor_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			return 0;
		}
	}

	public function getMalpracticeExpDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'non-surgical') {
			$model->where('user.profession_id', 'NOT IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.malpractice_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			return 0;
		}
	}

	public function getHpExpDateCount($mainModel)
	{
		$model = $this->pixie->orm->get('User_Credentials');
		$model->query = clone $mainModel->query;

		if ($this->_params['type'] == 'non-surgical') {
			$model->where('user.profession_id', 'NOT IN', $this->pixie->db->arr($this->medicalProfessionsIds));
			$model->where($this->pixie->db->expr('DATE(user_credentials.hp_exp_date)'), '<=', \Opake\Helper\TimeFormat::formatToDB(new \DateTime()));

			return $model->count_all();
		} else {
			return 0;
		}
	}
}
