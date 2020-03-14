<?php

namespace Opake\Model\Insurance;

use Opake\Helper\StringHelper;
use Opake\Model\AbstractModel;
use Opake\Model\Insurance\Data\AutoAccident;
use Opake\Model\Insurance\Data\Description;
use Opake\Model\Insurance\Data\Regular;
use Opake\Model\Insurance\Data\WorkersCompany;

abstract class AbstractType extends AbstractModel
{

	const INSURANCE_TYPE_COMMERCIAL = 1;
	const INSURANCE_TYPE_MEDICARE = 2;
	const INSURANCE_TYPE_MEDICAID = 3;
	const INSURANCE_TYPE_NO_FAULT = 4;
	const INSURANCE_TYPE_SELF_PAY = 5;
	const INSURANCE_TYPE_WORKERS_COMP = 6;
	const INSURANCE_TYPE_OTHER = 7;
	const INSURANCE_TYPE_AUTO_ACCIDENT = 8;
	const INSURANCE_TYPE_LOP = 9;
	const INSURANCE_TYPE_TRICARE = 10;
	const INSURANCE_TYPE_CHAMPVA = 11;
	const INSURANCE_TYPE_FECA_BLACK_LUNG = 12;

	/**
	 * @var Regular|AutoAccident|WorkersCompany|Description
	 */
	protected $insuranceDataModel;

	/**
	 * @return Regular|AutoAccident|WorkersCompany|Description
	 * @throws \Exception
	 */
	public function getInsuranceDataModel()
	{
		if ($this->insuranceDataModel === null) {
			$newModel = $model = $this->getNewInsuranceDataModelByType();
			if (!$this->insurance_data_id) {
				$this->insuranceDataModel = $newModel;
			} else {
				$model = $newModel->where('id', $this->insurance_data_id)->find();
				$this->insuranceDataModel = $model;
			}
		}

		return $this->insuranceDataModel;
	}

	/**
	 * @return \PHPixie\Validate\Validator
	 */
	public function getValidator()
	{
		return $this->getInsuranceDataModel()->getValidator();
	}

	/**
	 * @param AbstractType $insurance
	 */
	public function fromBaseInsurance(AbstractType $insurance)
	{
		$this->type = $insurance->type;
	}

	/**
	 * @param $data
	 */
	public function fill($data)
	{
		parent::fill($data);

		if (isset($data->data)) {
			$this->getInsuranceDataModel()->fill($data->data);
		}
	}

	public function save()
	{
		if ($this->insuranceDataModel) {
			$this->getInsuranceDataModel()->save();
			$this->insurance_data_id = $this->getInsuranceDataModel()->id();
		}

		parent::save();
	}

	/**
	 * @return Regular|AutoAccident|WorkersCompany|Description
	 * @throws \Exception
	 */
	public function getNewInsuranceDataModelByType()
	{
		if ($this->isAutoAccidentInsurance()) {
			return $this->pixie->orm->get('Insurance_Data_AutoAccident');
		}

		if ($this->isWorkersCompanyInsurance()) {
			return $this->pixie->orm->get('Insurance_Data_WorkersCompany');
		}

		if ($this->isDescriptionInsurance()) {
			return $this->pixie->orm->get('Insurance_Data_Description');
		}

		return $this->pixie->orm->get('Insurance_Data_Regular');
	}

	/**
	 * @return bool
	 */
	public function isAutoAccidentInsurance()
	{
		return ($this->type == static::INSURANCE_TYPE_AUTO_ACCIDENT);
	}

	/**
	 * @return bool
	 */
	public function isWorkersCompanyInsurance()
	{
		return ($this->type == static::INSURANCE_TYPE_WORKERS_COMP);
	}

	/**
	 * @return bool
	 */
	public function isDescriptionInsurance()
	{
		return ($this->type == static::INSURANCE_TYPE_LOP || $this->type == static::INSURANCE_TYPE_SELF_PAY);
	}

	/**
	 * @return bool
	 */
	public function isRegularInsurance()
	{
		return (!$this->isAutoAccidentInsurance() && !$this->isWorkersCompanyInsurance() && !$this->isDescriptionInsurance());
	}

	/**
	 * @return bool
	 */
	public function isInsuranceCompanyEqualsType()
	{
		return ($this->type == static::INSURANCE_TYPE_MEDICARE || $this->type == static::INSURANCE_TYPE_TRICARE ||
			$this->type == static::INSURANCE_TYPE_CHAMPVA || $this->type == static::INSURANCE_TYPE_FECA_BLACK_LUNG);
	}

	/**
	 * @return string
	 */
	public function getInsuranceTypeTitle()
	{
		$typesTitles = static::getInsuranceTypesList();
		return (isset($typesTitles[$this->type])) ? $typesTitles[$this->type] : '';
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		$typesTitles = static::getInsuranceTypesList();

		if ($this->isAutoAccidentInsurance() || $this->isWorkersCompanyInsurance()) {
			$type = $typesTitles[$this->type];
			$insuranceName = $this->getInsuranceDataModel()->insurance_company->name;
			if ($insuranceName) {
				$type .= ' - ' . $insuranceName;
			} else {
				$insuranceCompanyName = $this->getInsuranceDataModel()->insurance_name;
				if ($insuranceCompanyName && $insuranceCompanyName != $type) {
					$type .= ' - ' . $insuranceCompanyName;
				}
			}
			return $type;
		}

		if ($this->isDescriptionInsurance()) {
			$type = $typesTitles[$this->type];
			return $type;
		}

		if (isset($typesTitles[$this->type])) {
			$type = $typesTitles[$this->type];
			$insuranceName = $this->getInsuranceDataModel()->insurance->name;
			if ($insuranceName) {
				$type .= ' - ' . $insuranceName;
			} else {
				$insuranceCompanyName = $this->getInsuranceDataModel()->insurance_company_name;
				if ($insuranceCompanyName && $insuranceCompanyName != $type) {
					$type .= ' - ' . $insuranceCompanyName;
				}
			}

			return StringHelper::truncate($type, 80);
		}

		return '';
	}

	public function getInsuranceName()
	{
		$typesTitles = AbstractType::getInsuranceTypesList();

		if ($this->isAutoAccidentInsurance() || $this->isWorkersCompanyInsurance()) {
			$insuranceName = $this->getInsuranceDataModel()->insurance_company->name;
			if ($insuranceName) {
				return $insuranceName;
			} else {
				$insuranceCompanyName = $this->getInsuranceDataModel()->insurance_name;
				if ($insuranceCompanyName) {
					return $insuranceCompanyName;
				}

				return $typesTitles[$this->type];
			}
		} else if ($this->isDescriptionInsurance()) {

			return $typesTitles[$this->type];

		} if (isset($typesTitles[$this->type])) {

			$name = $typesTitles[$this->type];
			$insuranceName = $this->getInsuranceDataModel()->insurance->name;
			if ($insuranceName) {
				$name = $insuranceName;
			} else {
				$insuranceCompanyName = $this->getInsuranceDataModel()->insurance_company_name;
				if ($insuranceCompanyName) {
					$name = $insuranceCompanyName;
				}
			}

			return StringHelper::truncate($name, 80);
		}

		return '';
	}

	/**
	 * @return bool
	 */
	public function isEnoughDataForSave()
	{
		return true;
	}

	/**
	 * @return array
	 */
	public static function getInsuranceTypesList()
	{
		return [
			static::INSURANCE_TYPE_COMMERCIAL => 'Commercial',
			static::INSURANCE_TYPE_MEDICARE => 'Medicare',
			static::INSURANCE_TYPE_MEDICAID => 'Medicaid',
			static::INSURANCE_TYPE_NO_FAULT => 'No-Fault',
			static::INSURANCE_TYPE_SELF_PAY => 'Self-Pay',
			static::INSURANCE_TYPE_WORKERS_COMP => 'Workers Comp',
			static::INSURANCE_TYPE_OTHER => 'Other',
			static::INSURANCE_TYPE_AUTO_ACCIDENT => 'Auto Accident / No-Fault',
			static::INSURANCE_TYPE_LOP => 'LOP',
		    static::INSURANCE_TYPE_TRICARE => 'Tricare',
		    static::INSURANCE_TYPE_CHAMPVA => 'CHAMPVA',
		    static::INSURANCE_TYPE_FECA_BLACK_LUNG => 'FECA Black Lung'
		];
	}

	/**
	 * @return array
	 */
	public static function getInsuranceTypesListAcronym()
	{
		return [
			static::INSURANCE_TYPE_COMMERCIAL => 'C',
			static::INSURANCE_TYPE_MEDICARE => 'M',
			static::INSURANCE_TYPE_MEDICAID => 'MCD',
			static::INSURANCE_TYPE_NO_FAULT => 'A',
			static::INSURANCE_TYPE_SELF_PAY => 'S',
			static::INSURANCE_TYPE_WORKERS_COMP => 'W',
			static::INSURANCE_TYPE_OTHER => 'O',
			static::INSURANCE_TYPE_AUTO_ACCIDENT => 'A',
			static::INSURANCE_TYPE_LOP => 'LOP',
			static::INSURANCE_TYPE_TRICARE => 'T',
			static::INSURANCE_TYPE_CHAMPVA => 'CH',
			static::INSURANCE_TYPE_FECA_BLACK_LUNG => 'F'
		];
	}

	public static function getInsuranceOrderList()
	{
		return [
			0 => '',
		    1 => 'Primary',
		    2 => 'Secondary',
		    3 => 'Tertiary',
		    4 => 'Quaternary',
		    5 => 'Other'
		];
	}

	public static function getRegularInsuranceTypeIds()
	{
		return [
			static::INSURANCE_TYPE_COMMERCIAL,
			static::INSURANCE_TYPE_MEDICARE,
			static::INSURANCE_TYPE_MEDICAID,
			static::INSURANCE_TYPE_OTHER,
			static::INSURANCE_TYPE_TRICARE,
			static::INSURANCE_TYPE_CHAMPVA,
			static::INSURANCE_TYPE_FECA_BLACK_LUNG
		];
	}

	public static function getDescriptionInsuranceTypeIds()
	{
		return [
			static::INSURANCE_TYPE_SELF_PAY,
			static::INSURANCE_TYPE_LOP,
		];
	}
}