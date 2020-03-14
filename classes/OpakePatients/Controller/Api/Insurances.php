<?php

namespace OpakePatients\Controller\Api;

use OpakePatients\Controller\AbstractAjax;

class Insurances extends AbstractAjax
{
	public function actionGetPayorInfo()
	{
		$id = $this->request->get('id');
		$insurance = $this->orm->get('Insurance_Payor', $id);

		if ($insurance->loaded()) {
			$this->result = [
				'success' => true,
				'data' => $insurance->getFormatter('PayorInsuranceFill')->toArray()
			];
		} else {
			$this->result = [
				'success' => false
			];
		}
	}
}