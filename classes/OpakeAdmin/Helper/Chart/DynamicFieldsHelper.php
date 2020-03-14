<?php

namespace OpakeAdmin\Helper\Chart;

use Opake\Helper\TimeFormat;

class DynamicFieldsHelper
{
	/**
	 * @var \Opake\Application
	 */
	protected $pixie;

	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $caseItem;

	/**
	 * @var array
	 */
	protected $dynamicFieldValues;

	/**
	 * @param \Opake\Model\Cases\Item $caseItem
	 */
	public function __construct($caseItem)
	{
		$this->pixie = \Opake\Application::get();
		$this->caseItem = $caseItem;
		$this->dynamicFieldValues = $this->extractValues($this->caseItem);
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public function replaceDynamicFields($text)
	{
		$values = $this->dynamicFieldValues;
		$tags = $this->getDynamicFieldsTags();

		foreach ($tags as $tagName => $tag) {
			$valueToReplace = (isset($values[$tagName])) ? $values[$tagName] : '';
			$text = str_replace($tag, $valueToReplace, $text);
		}

		return $text;
	}

	/**
	 * @param \Opake\Model\Cases\Item $caseItem
	 * @return array
	 */
	protected function extractValues($caseItem)
	{
		$data = [];

		/** @var \Opake\Model\Patient $patient */
		$patient = $caseItem->registration->patient;

		$data['firstName'] = $patient->first_name;
		$data['lastName'] = $patient->last_name;
		$data['age'] = $patient->getAge();
		$data['dob'] = $patient->dob ? TimeFormat::fromDBDate($patient->dob)->format('m/d/Y') : '';
		$data['gender'] = $patient->getGender();
		$data['gender2'] =  $this->getPatientGenderTitle($patient);
		$data['street'] = $patient->home_address;
		$data['city'] =  (($patient->custom_home_city) ? : (($patient->home_city->loaded()) ? $patient->home_city->name : ''));
		$data['state'] = (($patient->custom_home_state) ? : (($patient->home_state->loaded()) ? $patient->home_state->name : ''));
		$data['country'] =  ($patient->home_country->loaded()) ? $patient->home_country->name : '';
		$data['zip'] = $patient->home_zip_code;
		$data['mrn'] = $patient->getFullMrn();
		$data['physician'] = $caseItem->getSurgeonNames();
		$data['dos'] = $caseItem->time_start ? TimeFormat::fromDBDatetime($caseItem->time_start)->format('m/d/Y') : '';
		$data['insurance'] = $caseItem->registration->getPrimaryInsuranceTitle();

		$site = $caseItem->location->site;

		$data['siteName'] = $site->name;
		$data['siteAddress'] =  $site->address;
		$data['siteCity'] = ($site->custom_city !== null) ? $site->custom_city :
			($site->city && $site->city->loaded()) ? $site->city->name : null;;
		$data['siteState'] = ($site->custom_state !== null) ? $site->custom_state :
			($site->state && $site->state->loaded()) ? $site->state->name : null;;
		$data['siteCountry'] = ($site->country && $site->country->loaded()) ? $site->country->name : '';
		$data['siteZip'] = $site->zip_code;
		$data['sitePhone'] = $site->contact_phone;
		$data['account'] = $caseItem->id();
		$data['apt'] = $patient->home_apt_number;

		return $data;
	}

	/**
	 * @param $patient
	 * @return string
	 */
	protected function getPatientGenderTitle($patient)
	{
		return $patient->getGenderTitle();
	}

	/**
	 * @return array
	 */
	protected function getDynamicFieldsTags()
	{
		return [
			'firstName' => '%FirstName%',
			'lastName' => '%LastName%',
			'age' => '%Age%',
			'dob' => '%DOB%',
			'gender' => '%Gender%',
			'gender2' => '%Gender2%',
			'street' => '%Street%',
			'city' => '%City%',
			'state' => '%State%',
			'country' => '%Country%',
			'zip' => '%Zip%',
			'mrn' => '%MRN%',
			'physician' => '%Physician%',
			'dos' => '%DOS%',
			'insurance' => '%Insurance%',
			'siteName' => '%SiteName%',
			'siteAddress' => '%SiteAddress%',
			'siteCity' => '%SiteCity%',
			'siteState' => '%SiteState%',
			'siteCountry' => '%SiteCountry%',
			'siteZip' => '%SiteZip%',
			'sitePhone' => '%SitePhone%',
		    'account' => '%Account%',
		    'apt' => '%Apt%'
		];
	}

}