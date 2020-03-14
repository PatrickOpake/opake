<?php

namespace OpakeAdmin\Events\Alert;

use DateTime;
use Opake\Model\User\Credentials\Alert;

class Credentials extends \Opake\Events\AbstractListener
{

	public function dispatch($credential)
	{
		if ( !$credential->loaded() ) {
			return false;
		}

		$fields = $this->getDates($credential);

		if($fields) {
			$this->orm->get('User_Credentials_Alert')->where([
				['credentials_id', $credential->id()],
				['field', 'NOT IN', $this->pixie->db->arr($fields)],
			])->delete_all();
		} else {
			$this->orm->get('User_Credentials_Alert')->where([
				['credentials_id', $credential->id()],
			])->delete_all();
		}

		$this->pixie->db->begin_transaction();
		try {
			$alerts = $this->orm->get('User_Credentials_Alert')
				->where([
					['credentials_id', $credential->id()],
					['field', 'IN', $this->pixie->db->arr($fields)],
				])->find_all();
			$existedAlertFields = [];
			foreach ($alerts as $alert) {
				$existedAlertFields[] = $alert->field;
			}
			foreach ($fields as $fieldName) {
				if(!in_array($fieldName, $existedAlertFields)) {
					$alert = $this->orm->get('User_Credentials_Alert');
					$alert->credentials_id = $credential->id;
					$alert->status = Alert::STATUS_ACTIVE;
					$alert->field = $fieldName;
					$alert->save();
				}
			}
			$this->pixie->db->commit();
		} catch (\Exception $e) {
			$this->pixie->db->rollback();
			throw $e;
		}
	}


	protected function getDates($credential)
	{
		$result = [];
		$now2 = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime('+90 day'));
		$data = [
			'medical_licence_exp_date' => $credential->medical_licence_exp_date,
			'dea_exp_date' => $credential->dea_exp_date,
			'cds_exp_date' => $credential->cds_exp_date,
			'insurance_exp_date' => $credential->insurance_exp_date,
			'insurance_reappointment_date' => $credential->insurance_reappointment_date,
			'immunizations_ppp_due' => $credential->immunizations_ppp_due,
			'immunizations_help_b' =>  $credential->immunizations_help_b,
			'immunizations_rubella' => $credential->immunizations_rubella,
			'immunizations_rubeola' => $credential->immunizations_rubeola,
			'immunizations_varicela' => $credential->immunizations_varicela,
			'immunizations_mumps' => $credential->immunizations_mumps,
			'immunizations_flue' => $credential->immunizations_flue,
			'retest_date' => $credential->retest_date,
			'licence_expr_date' => $credential->licence_expr_date,
			'bls_date' => $credential->bls_date,
			'acls_date' => $credential->acls_date,
			'cnor_date' => $credential->cnor_date,
			'malpractice_exp_date' => $credential->malpractice_exp_date,
			'hp_exp_date' => $credential->hp_exp_date,
		];

		foreach ($data as $key => $date) {
			if($date && new DateTime($date) < new DateTime($now2)) {
				$result[] = $key;
			}
		}

		return $result;
	}


}
