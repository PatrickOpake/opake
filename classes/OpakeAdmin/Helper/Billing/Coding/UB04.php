<?php

namespace OpakeAdmin\Helper\Billing\Coding;

use Opake\Helper\StringHelper;

class UB04 extends AbstractForm
{
	/**
	 * PDF parameters
	 */
	protected $useTemplate = false;
	protected $cellBorder = 0;
	protected $font = 'Arial';
	protected $fontSize = 8;
	protected $lineHeight = 4.23;
	protected $paddingLeft = 3.8;
	protected $paddingTop = 4.7;

	/**
	 * @param \Opake\Model\Cases\Item $case
	 * @throws \Exception
	 */
	public function __construct($case)
	{
		parent::__construct($case);

		if ($this->pixie->config->has('app.coding.forms_use_template')) {
			$this->useTemplate = $this->pixie->config->get('app.coding.forms_use_template');
		}
	}

	/**
	 * Draw cell with content
	 * @param float $x
	 * @param int $row
	 * @param float $w
	 * @param string $content
	 */
	protected function drawCell($x, $row, $w, $content, $align = 'L')
	{
		$this->pdf->SetXY($this->paddingLeft + $x, $this->paddingTop + $this->lineHeight * ($row - 1));
		$this->pdf->Cell($w, $this->lineHeight, $content, $this->cellBorder, 0, $align);
	}

	/**
	 * Return formatted name
	 * @param \Opake\Model\Cases\Registration|\Opake\Model\Insurance\Data\Regular $object
	 * @return string
	 */
	protected function getFullName($object)
	{
		$lastName = $object->last_name;
		if ($object->suffix) {
			$listValues = \Opake\Model\Patient::getSuffixesList();
			if (isset($listValues[$object->suffix])) {
				$lastName .= ' ' . $listValues[$object->suffix];
			}
		}
		$firstName = $object->first_name;
		if ($object->middle_name) {
			$firstName .= ' ' . $object->middle_name;
		}
		return $lastName . ', ' . $firstName;
	}

	/**
	 * @return string
	 */
	public function compile()
	{
		$this->pdf->AddPage('P','letter');
		$this->pdf->SetXY(0, 0);
		$this->pdf->SetMargins(0,0);
		$this->pdf->SetAutoPageBreak(false);
		$this->pdf->SetFont($this->font, '', $this->fontSize);

		if ($this->useTemplate) {
			$this->pdf->Image($this->pixie->app_dir . 'docs/UB04.jpg', 0, 0, $this->pdf->GetPageWidth(), $this->pdf->GetPageHeight());
		}

		// Data objects
		$case = $this->case;
		$firstSurgeon = $case->getFirstSurgeon();
		$site = $case->location->site;
		$registration = $case->registration;

		$coding = $case->coding;
		$primaryInsurance = $coding->getPrimaryInsurance();
		$secondaryInsurance = $coding->getSecondaryInsurance();
		$tertiaryInsurance = $coding->getTertiaryInsurance();

		$insurances = [
			$primaryInsurance,
			$secondaryInsurance,
		    $tertiaryInsurance
		];


		if (!$coding->isPrimaryInsuranceAssigned()) {
			$mainInsurance = $coding->getAssignedInsurance();
		} else {
			$mainInsurance = $primaryInsurance;
		}

		$fullPatientName = $this->getFullName($registration);

		$dos = new \DateTime($case->time_start);
		$dosStr = $dos->format('mdy');

		$addressParts = [];
		if ($registration->home_apt_number) {
			$addressParts[] = $registration->home_apt_number;
		}
		if ($registration->home_address) {
			$addressParts[] = $registration->home_address;
		}
		$patientAddress = implode(' ', $addressParts);

		// 01 Site Info
		$siteName = $site->name;
		$siteCityStateZip = trim($site->getCityName() . ' ' . $site->state->code . ' ' . $site->zip_code);
		$this->drawCell(6.5, 1, 62.2, StringHelper::truncate($siteName, 45, ''));
		$this->drawCell(6.5, 2, 62.2, StringHelper::truncate($site->address, 45, ''));
		$this->drawCell(6.5, 3, 62.2, StringHelper::truncate($siteCityStateZip, 45, ''));
		$this->drawCell(6.5, 4, 62.2, StringHelper::truncate($site->contact_phone, 45, ''));

		// 02 Site Pay Info

		if (preg_replace('/\s+/', '', trim($site->address)) != preg_replace('/\s+/', '', trim($site->pay_address))) {
			$sitePayCityStateZip = trim($site->getPayCityName() . ' ' . $site->pay_state->code . ' ' . $site->pay_zip_code);

			$this->drawCell(64.7, 1, 62.2, StringHelper::truncate($site->pay_name, 45, ''));
			$this->drawCell(64.7, 2, 62.2, StringHelper::truncate($site->pay_address, 45, ''));
			$this->drawCell(64.7, 3, 62.2, StringHelper::truncate($sitePayCityStateZip, 45, ''));
		}

		// 3a Account #
		$this->drawCell(134.4, 1, 61, $case->id);

		// 3b MRN
		$this->drawCell(134.4, 2, 61, $case->registration->patient->getFullMrn());

		// 4 Type of bill
		/*if (!is_null($coding->bill_type)) {
			$billType = (int) $coding->bill_type;
			$billTypeMapRules = [
				6 => 7,
				7 => 8,
			];
			if (isset($billTypeMapRules[$billType])) {
				$billType = $billTypeMapRules[$billType];
			}
			$this->drawCell(195.4, 2, 13, '083' . $billType);
		}*/
		if ($coding->original_claim_id) {
			$this->drawCell(195.4, 2, 13, '837');
		}
		else {
			//hardcode 0831 instead all values
			$this->drawCell(195.4, 2, 13, '831');
		}

		// Federal Tax Number
		$this->drawCell(126.9, 4, 25.6, $site->federal_tax);

		// DOS
		$this->drawCell(152.5, 4, 17.8, $dosStr);
		$this->drawCell(170.3, 4, 17.8, $dosStr);

		// Patient Name
		$this->drawCell(2.7, 6, 73.5, $fullPatientName);

		// 9a Patient Address
		$this->drawCell(104.2, 5, 104.3, $patientAddress);
		$this->drawCell(78.8, 6, 81, $registration->getHomeCityName());
		if ($registration->home_country_id == \Opake\Model\Geo\Country::US_ID) {
			$this->drawCell(162.7, 6, 7.5, $registration->home_state->code);
		}
		$this->drawCell(172.5, 6, 25.6, $registration->home_zip_code);
		$this->drawCell(200.8, 6, 7.6, 'US');

		// Patient DOB
		$dob = (new \DateTime($registration->dob))->format('mdY');
		$this->drawCell(0, 8, 22.8, $dob);

		// Patient Gender
		if ($registration->gender) {
			if ($registration->gender == \Opake\Model\Patient::GENDER_MALE) {
				$gender = 'M';
			} elseif ($registration->gender == \Opake\Model\Patient::GENDER_FEMALE) {
				$gender = 'F';
			} else {
				$gender = 'U';
			}
			$this->drawCell(22.8, 8, 7.6, $gender);
		}

		// DOS 2
		//$this->drawCell(30.4, 8, 15.2, $dosStr);

		// Admission Hour
		//$this->drawCell(45.6, 8, 7.65, $dos->format('H'));

		// Admission Type
		if ($registration->admission_type && $registration->admission_type != \Opake\Model\Cases\Registration::ADMISSION_TYPE_INFO_NOT_AVAIL) {
			$this->drawCell(53.25, 8, 7.65, $registration->admission_type);
		}

		// Point of Origin for Admission or Visit
		if ($case->point_of_origin) {
			$value = (int) $case->point_of_origin;
			$mapRules = [
				1 => 1,
				2 => 2,
				3 => 4,
				4 => 5,
				5 => 6,
				6 => 7,
				7 => 8,
				8 => 9,
				9 => 'D',
				10 => 'E',
				11 => 'F'
			];
			if (isset($mapRules[$value])) {
				$this->drawCell(60.9, 8, 7.65, $mapRules[$value]);
			}
		}

		// Code Inputs
		if ($coding->loaded()) {
			// 17
			if ($coding->discharge_code_id && $coding->discharge_code->loaded()) {
				$this->drawCell(76.3, 8, 7.65, $coding->discharge_code->code);
			}

			// 18-28
			$i = 0;
			$cellWidth = 7.62;
			foreach ($coding->condition_codes->find_all() as $code) {
				if ($i <= 10) {
					$this->drawCell(83.95 + $cellWidth * $i, 8, $cellWidth, substr($code->code, 0, 2));
					$i++;
				}
			}

			if ($primaryInsurance && $primaryInsurance->getCaseInsurance()->isAutoAccidentInsurance()) {
				$this->drawCell(167.6, 8, 8.3, $primaryInsurance->getState() ? $primaryInsurance->getState()->code : '');
			} else if ($secondaryInsurance && $secondaryInsurance->getCaseInsurance()->isAutoAccidentInsurance()) {
				$this->drawCell(167.6, 8, 8.3, $secondaryInsurance->getState() ? $secondaryInsurance->getState()->code : '');
			}

			// 31-34
			$i = 0;
			$groupWidth = 25.4;
			$occurrenceGroups = array_chunk($coding->occurrences->find_all()->as_array(), 2);
			foreach ($occurrenceGroups as $occurrenceGroup) {
				if ($i < 4) {
					$row = 0;
					foreach ($occurrenceGroup as $occurrence) {
						if ($occurrence->occurrence_code->loaded()) {
							$this->drawCell(-1 + $groupWidth * $i, 10 + $row, 7.7, $occurrence->occurrence_code->code);
						}
						if ($occurrence->date) {
							$date = (new \DateTime($occurrence->date))->format('mdy');
							$this->drawCell(7.7 + $groupWidth * $i, 10 + $row, 17.7, $date);
						}
						$row++;
					}
					$i++;
				}
			}
		}

		// 38 Bill responsible info
		$billResponsibleInfo = [];

		if ($mainInsurance) {
			$billResponsibleInfo['name'] = $mainInsurance->getInsuranceCompanyName();
			$billResponsibleInfo['address'] = $mainInsurance->getAddress();
			$address2 = [];
			if ($mainInsurance->getCity()) {
				$address2[] = $mainInsurance->getCity()->name;
			}
			if ($mainInsurance->getState()) {
				$address2[] = $mainInsurance->getState()->code;
			}
			if ($mainInsurance->getZipCode()) {
				$address2[] = $mainInsurance->getZipCode();
			}
			$billResponsibleInfo['address2'] = implode(' ', $address2);

			$this->drawCell(22.8, 12, 103.6, $billResponsibleInfo['name']);
			$this->drawCell(22.8, 13, 103.6, $billResponsibleInfo['address']);
			$this->drawCell(22.8, 14, 103.6, $billResponsibleInfo['address2']);
		}


		// 39-41 Value Codes and Amounts
		if ($coding->loaded()) {
			$values = $coding->values->with('value_code')
				->order_by($this->pixie->db->expr('ISNULL(value_code.code)'))
				->order_by('value_code.code')
				->find_all()->as_array();

			$i = 0;
			$groupWidth = 33;
			foreach (array_chunk($values, 4) as $valuesGroup) {
				if ($i < 4) {
					$row = 0;
					foreach ($valuesGroup as $value) {
						if ($value->value_code->code) {
							$this->drawCell(109.3 + $groupWidth * $i, 13 + $row, 7.7, $value->value_code->code, 'C');
						}
						if ($value->amount) {
							$this->drawCell(117 + $groupWidth * $i, 13 + $row, 25.3, $this->formatMoney($value->amount), 'R');
						}
						$row++;
					}
					$i++;
				}
			}
		}

		// 42-48 Billing
		$totalCharge = 0;
		if ($coding->loaded()) {
			$i = 0;
			foreach ($coding->bills->find_all() as $bill) {
				$chargeMasterRecord = $bill->getChargeMasterEntry();
				$fee = null;

				$revenueCode = $bill->revenue_code;
				$description = null;
				$codes = [];
				$serviceUnits = $bill->quantity;
				$modifiers = [];
				if ($chargeMasterRecord) {
					$fee = $chargeMasterRecord->getFeeScheduleEntry();
					$description = $chargeMasterRecord->desc;
					$codes[] = $chargeMasterRecord->cpt;
					if ($bill->custom_modifier) {
						$modifiers = $bill->custom_modifier;
					} else {
						$modifiers = $chargeMasterRecord->getModifiersTitle();
					}
				}
				if ($modifiers) {
					$codes[] = substr($modifiers, 0, 20);
				}

				if ($revenueCode) {
					$this->drawCell(0, 18 + $i, 11.7, $revenueCode);
				}
				if ($description) {
					$description = StringHelper::truncate($description, 38, '');
					$this->drawCell(11.7, 18 + $i, 63.5, $description);
				}
				$this->drawCell(75.2, 18 + $i, 38.2, implode(' ', $codes));
				$this->drawCell(113.4, 18 + $i, 17.8, $dosStr);
				if ($serviceUnits) {
					$this->drawCell(131.2, 18 + $i, 20.2, $serviceUnits);
				}
				$chargesSum = 0;
				if ($bill->charge) {
					$chargesSum = ($bill->quantity !== '' && $bill->quantity !== null) ? ($bill->charge * $bill->quantity) : $bill->charge;
					$this->drawCell(151.4, 18 + $i, 25.5, $this->formatMoney($chargesSum));
				}
				if ($fee && $fee->non_covered_charges) {
					$this->drawCell(176.9, 18 + $i, 25, $this->formatMoney($fee->non_covered_charges));
				}

				$totalCharge += $chargesSum;
				$i++;
			}
		}

		$this->drawCell(0, 40, 11.7, '0001');
		$this->drawCell(25, 40, 3.5, 1);
		$this->drawCell(40.5, 40, 3.5, 1);
		$this->drawCell(113.4, 40, 17.8, (new \DateTime())->format('mdY'));
		$this->drawCell(151.4, 40, 25.5, $this->formatMoney($totalCharge));

		// 50-54 Payer Name
		for ($i = 0; $i < 3; $i++) {

			/** @var \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance $insurance */
			$insurance = isset($insurances[$i]) ? $insurances[$i] : null;
			$payerId = null;
			$payerName = null;

			if ($insurance) {
				$payerName = $insurance->getInsuranceCompanyName();
			} else {
				continue;
			}

			if (!empty($payerName)) {
				$this->drawCell(0, 42 + $i, 58.4, substr($payerName, 0, 23));
				$this->drawCell(96.6, 42 + $i, 5, $coding->authorization_release_information_payment ? 'Y' : 'I');
				$this->drawCell(104.2, 42 + $i, 5, $coding->authorization_release_information_payment ? 'Y' : 'N');
			}

		}

		//54 Prior Payments
		$amountPaid = '0.00';
		if ($coding->isPrimaryInsuranceAssigned() && $coding->amount_paid) {
			$amountPaid = $coding->amount_paid;
		} elseif($coding->amount_paid_by_other_insurance) {
			$amountPaid = $coding->amount_paid_by_other_insurance;
		}
		$this->drawCell(109.2, 42, 25.2, $this->formatMoney($amountPaid));


		//55 Amount Due
		$this->drawCell(134.5, 42, 28, $this->formatMoney($totalCharge));

		// 56 Org NPI
		if ($site->npi) {
			$this->drawCell(170, 41, 38.4, $site->npi);
		}

		// 58-62 Insurances
		$relationshipToInsuredmapRules = [
			0 => '18',
			5 => '19',
			7 => '01',
			8 => '20',
			9 => '21',
			10 => '39',
			11 => '40',
			12 => '53',
			13 => 'G8'
		];

		for ($i = 0; $i < 3; $i++) {
			if (isset($insurances[$i])) {
				/** @var \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance $insurance */
				$insurance = $insurances[$i];

				if ($insurance->getCaseInsurance()->isRegularInsurance() ||
					$insurance->getCaseInsurance()->isWorkersCompanyInsurance() ||
					$insurance->getCaseInsurance()->isAutoAccidentInsurance()) {

					$insuranceName = $fullPatientName;
					if ($insurance->getCaseInsurance()->isRegularInsurance()) {
						$insuranceDataModel = $insurance->getCaseInsurance()->getInsuranceDataModel();
						if ($insuranceDataModel->relationship_to_insured != 0) {
							$insuranceName = $this->getFullName($insuranceDataModel);
						}

						$relationshipToInsured = (int) $insuranceDataModel->relationship_to_insured;
						if (isset($relationshipToInsuredmapRules[$relationshipToInsured])) {
							$this->drawCell(66, 46 + $i, 7.6, $relationshipToInsuredmapRules[$relationshipToInsured]);
						}
					}

					if ($insurance->getCaseInsurance()->isWorkersCompanyInsurance() || $insurance->getCaseInsurance()->isAutoAccidentInsurance()) {
						$this->drawCell(66, 46 + $i, 7.6, '18'); //always self
					}

					$this->drawCell(0, 46 + $i, 66, $insuranceName);

					if ($insurance->getGroupNumber()) {
						$this->drawCell(162.5, 46 + $i, 46, $insurance->getGroupNumber());
					}
					if ($insurance->getPolicyNumber()) {
						$this->drawCell(73.6, 46 + $i, 50.8, $insurance->getPolicyNumber());
					}

				}
			}
		}

		// 63-65 Insurances
		for ($i = 0; $i < 3; $i++) {
			/** @var \OpakeAdmin\Helper\Billing\Insurance\AbstractInsurance $insurance */
			$insurance = (isset($insurances[$i])) ? $insurances[$i] : null;
			if (!$insurance) {
				continue;
			}

			if ($insurance->getCaseInsurance()->isRegularInsurance()) {
				$insuranceDataModel = $insurance->getCaseInsurance()->getInsuranceDataModel();
				$this->drawCell(0, 50 + $i, 78.8, $insuranceDataModel->authorization_or_referral_number);
			}
			// this field always empty
			// see OPK-3876
			// $this->drawCell(78.8, 50 + $i, 66, $insurance->getPrevAdjudicatedClaimNumber());
			$employer = null;
			if ($insurance->getCaseInsurance()->type == \Opake\Model\Insurance\AbstractType::INSURANCE_TYPE_OTHER && $registration->employer) {
				$employer = $registration->employer;
			}
			if ($insurance->getCaseInsurance()->isWorkersCompanyInsurance()) {
				$insuranceDataModel = $insurance->getCaseInsurance()->getInsuranceDataModel();
				if ($insuranceDataModel->employer_name) {
					$employer = $insuranceDataModel->employer_name;
				}
			}
			if ($employer) {
				$this->drawCell(144.8, 50 + $i, 63.7, $employer);
			}
		}

		// 64 Document Control Number
		if ($coding->original_claim_id) {
			$this->drawCell(78.8, 50, 5, $coding->original_claim_id);
		}

		// 66 Diagnosis and Procedure Code Qualifier (ICD Revision Indicator)
		$this->drawCell(-0.3, 54, 3, 0);

		// 67 Principal Diagnosis Code and Present of Admission Indicator
		if ($coding->loaded()) {
			foreach ($coding->diagnoses->find_all() as $diagnosis) {
				$codingRow = (int) $diagnosis->row - 1;
				$row = $codingRow > 8 ? 1 : 0;
				$indent = ($row === 1 ? ($codingRow % 8 - 1) : $codingRow) * 20.3;
				$this->drawCell(2.7 + $indent, 53 + $row, 20.3, $diagnosis->icd->code);
			}
		}

		if ($firstSurgeon) {
			$credentials = $firstSurgeon->credentials;

			// 76 Attending Provider Name and Identifiers
			if ($credentials->npi_number) {
				$this->drawCell(149, 56, 26, $credentials->npi_number);
			}
			$this->drawCell(133, 57, 39.9, $firstSurgeon->last_name);
			$this->drawCell(179, 57, 29.5, $firstSurgeon->first_name);

			// 77 Operating Physician Name and Identifiers
			if ($credentials->npi_number) {
				$this->drawCell(149, 58, 26, $credentials->npi_number);
			}
			$this->drawCell(133, 59, 39.9, $firstSurgeon->last_name);
			$this->drawCell(179, 59, 29.5, $firstSurgeon->first_name);
		}

		// 80 Remarks
		if ($coding->remarks) {
			$remarks = $coding->remarks;
			$charsByRow = [19, 24, 24, 24];
			$degaultChars = 24;
			$i = 0;
			foreach ($charsByRow as $chars) {
				$row = substr($remarks, 0, $chars);
				$remarks = substr($remarks, $chars);
				$diffChars = ($degaultChars - $chars) * 3;
				$this->drawCell(0 + $diffChars, 60 + $i, 60 - $diffChars, $row);
				$i++;
			}
		}

		return $this->pdf->Output('S');
	}

	protected function formatMoney($float)
	{
		return number_format((float) $float, 2, '.', '');
	}
}
