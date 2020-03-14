<?php

namespace OpakeAdmin\Events\Timer;

class Credentials extends \Opake\Events\AbstractListener
{

	public function dispatch($obj)
	{
		$now2 = strftime(\Opake\Helper\TimeFormat::DATE_FORMAT_DB, strtotime('+90 day'));

		$model = $this->orm->get('User_Credentials');
		$model->where([
			['medical_licence_exp_date', '<', $now2],
			['or', ['dea_exp_date', '<', $now2]],
			['or', ['cds_exp_date', '<', $now2]],
			['or', ['insurance_exp_date', '<', $now2]],
			['or', ['insurance_reappointment_date', '<', $now2]],
			['or', ['immunizations_ppp_due', '<', $now2]],
			['or', ['immunizations_help_b', '<', $now2]],
			['or', ['immunizations_rubella', '<', $now2]],
			['or', ['immunizations_rubeola', '<', $now2]],
			['or', ['immunizations_varicela', '<', $now2]],
			['or', ['immunizations_mumps', '<', $now2]],
			['or', ['immunizations_flue', '<', $now2]],
			['or', ['retest_date', '<', $now2]],
			['or', ['licence_expr_date', '<', $now2]],
			['or', ['bls_date', '<', $now2]],
			['or', ['acls_date', '<', $now2]],
			['or', ['cnor_date', '<', $now2]],
			['or', ['malpractice_exp_date', '<', $now2]],
			['or', ['hp_exp_date', '<', $now2]],
		]);

		foreach ($model->find_all() as $item) {
			$this->pixie->events->fireEvent('update.expiring_credentials', $item);
		}
	}

}
