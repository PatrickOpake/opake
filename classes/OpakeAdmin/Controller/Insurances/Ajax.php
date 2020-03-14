<?php

namespace OpakeAdmin\Controller\Insurances;

use Opake\Exception\BadRequest;

class Ajax extends \OpakeAdmin\Controller\Ajax
{

	public function before()
	{
		parent::before();
	}

	public function actionInsurances()
	{
		$this->result = [];
		$q = $this->request->get('query');
		$insuranceType = $this->request->get('insuranceType');
		$includeEmpty = $this->request->get('includeEmpty');
		$insurance = $this->orm->get('Insurance_Payor');

		$insurance->where(['actual', 1]);

		if($insuranceType !== null && $insuranceType !== '') {
			$insurance->where(['insurance_type', $insuranceType]);
		}

		if ($q !== null && $q !== '') {

			$insurance->where(['name', 'like', '%' . $q . '%']);
			$insurance->order_by('name', 'asc')->limit(12);

			foreach ($insurance->find_all() as $item) {
				$this->result[] = $item->toArray();
			}

		} else {

			if ($includeEmpty) {
				$this->result[] = [
					'id' => null,
					'name' => ''
				];
			}

			$insurance->order_by('name', 'asc')->limit(12);

			foreach ($insurance->find_all() as $item) {
				$this->result[] = $item->toArray();
			}
		}
	}

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

	public function actionGetPossiblePayorAddresses()
	{
		$id = $this->request->get('id');
		if (!$id) {
			throw new BadRequest('Bad Request');
		}
		$insurance = $this->orm->get('Insurance_Payor', $id);

		$result = [];
		if ($insurance->loaded()) {
			foreach ($insurance->addresses->find_all() as $addressModel) {
				$result[] = $addressModel->getFormatter('InsuranceFill')->toArray();
			}
		}

		$this->result = $result;

	}

}
