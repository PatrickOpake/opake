<?php

namespace OpakeAdmin\Helper\Insurance;

use Opake\Helper\TimeFormat;
use Opake\Model\Insurance\AbstractType;

class InsurancePayorHelper
{
	/**
	 * @param AbstractType $insurance
	 */
	public static function updatePayorData($insurance)
	{
		if (!$insurance->isDescriptionInsurance()) {

			if ($insurance->isWorkersCompanyInsurance() || $insurance->isAutoAccidentInsurance()) {
				static::updateWorkersAndAutoAccident($insurance);
			} else {
				static::updateCommercial($insurance);
			}
		}
	}

	/**
	 * @param AbstractType $insurance
	 */
	protected static function updateWorkersAndAutoAccident($insurance)
	{
		$app = \Opake\Application::get();
		$insuranceType = $insurance->type;
		$insuranceDataModel = $insurance->getInsuranceDataModel();

		$insurancePayorModel = $insuranceDataModel->insurance_company;
		if ($insurancePayorModel->loaded()) {
			$insurancePayorModel->insurance_type = $insuranceType;

			$addressModel = null;
			if ($insuranceDataModel->isNewAddressEntered()) {
				$addressModel = $app->orm->get('Insurance_Payor_Address');
				$addressModel->payor_id = $insurancePayorModel->id();
			} else {
				if ($insuranceDataModel->selected_insurance_company_address_id) {
					$addressModel = $app->orm->get('Insurance_Payor_Address', $insuranceDataModel->selected_insurance_company_address_id);
				}
			}

			if ($addressModel) {
				$addressModel->address = $insuranceDataModel->insurance_address;
				$addressModel->phone = $insuranceDataModel->insurance_company_phone;
				$addressModel->state_id = $insuranceDataModel->state_id;
				$addressModel->city_id = $insuranceDataModel->city_id;
				$addressModel->zip_code = $insuranceDataModel->zip;
			}

			$insurancePayorModel->navicure_eligibility_payor_id = $insuranceDataModel->eligibility_payer_id;
			$insurancePayorModel->ub04_payer_id = $insuranceDataModel->ub04_payer_id;
			$insurancePayorModel->cms1500_payer_id = $insuranceDataModel->cms1500_payer_id;

			if ($user = $app->auth->user()) {
				$insurancePayorModel->last_change_user_id = $user->id();
			}

			$currentDate = new \DateTime();
			$insurancePayorModel->last_change_date = TimeFormat::formatToDBDatetime($currentDate);

			$insurancePayorModel->save();
			if ($addressModel) {
				$addressModel->save();
				if ($insuranceDataModel->isNewAddressEntered()) {
					$insuranceDataModel->selected_insurance_company_address_id = $addressModel->id();
					$insuranceDataModel->save();
					$insuranceDataModel->setIsNewAddressEntered(false);
				}
			}
		}
	}

	/**
	 * @param AbstractType $insurance
	 */
	protected static function updateCommercial($insurance)
	{
		$app = \Opake\Application::get();
		$insuranceType = $insurance->type;
		$insuranceDataModel = $insurance->getInsuranceDataModel();

		$insurancePayorModel = $insuranceDataModel->insurance;
		if ($insurancePayorModel->loaded()) {
			$insurancePayorModel->insurance_type = $insuranceType;

			$addressModel = null;
			if ($insuranceDataModel->isNewAddressEntered()) {
				$addressModel = $app->orm->get('Insurance_Payor_Address');
				$addressModel->payor_id = $insurancePayorModel->id();
			} else {
				if ($insuranceDataModel->selected_insurance_address_id) {
					$addressModel = $app->orm->get('Insurance_Payor_Address', $insuranceDataModel->selected_insurance_address_id);
				}
			}

			if ($addressModel) {
				$addressModel->address = $insuranceDataModel->address_insurance;
				$addressModel->phone = $insuranceDataModel->provider_phone;
				$addressModel->state_id = $insuranceDataModel->insurance_state_id;
				$addressModel->city_id = $insuranceDataModel->insurance_city_id;
				$addressModel->zip_code = $insuranceDataModel->insurance_zip_code;
			}

			$insurancePayorModel->navicure_eligibility_payor_id = $insuranceDataModel->eligibility_payer_id;
			$insurancePayorModel->ub04_payer_id = $insuranceDataModel->ub04_payer_id;
			$insurancePayorModel->cms1500_payer_id = $insuranceDataModel->cms1500_payer_id;

			if ($user = $app->auth->user()) {
				$insurancePayorModel->last_change_user_id = $user->id();
			}

			$currentDate = new \DateTime();
			$insurancePayorModel->last_change_date = TimeFormat::formatToDBDatetime($currentDate);

			$insurancePayorModel->save();
			if ($addressModel) {
				$addressModel->save();
				if ($insuranceDataModel->isNewAddressEntered()) {
					$insuranceDataModel->selected_insurance_address_id = $addressModel->id();
					$insuranceDataModel->save();
					$insuranceDataModel->setIsNewAddressEntered(false);
				}
			}
		}
	}
}