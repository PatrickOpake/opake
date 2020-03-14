<?php

namespace OpakeAdmin\Helper\Billing\Coding;

use Opake\Helper\TimeFormat;
use Opake\Model\Cases\Coding;
use Opake\Model\Cases\Registration;
use Opake\Model\Insurance\AbstractType;
use Opake\Model\Patient;

class CMS1500 extends AbstractForm
{
	/**
	 * PDF parameters
	 */
	protected $useTemplate = false;
	protected $cellBorder = 0;
	protected $font = 'Arial';
	protected $fontSize = 8;
	protected $lineHeight = 4.23;
	protected $paddingLeft = 5.8;
	protected $paddingTop = 20;

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
	 * @param float $y
	 * @param float $w
	 * @param string $content
	 */
	protected function drawCell($x, $y, $w, $content)
	{
		$this->pdf->SetXY($this->paddingLeft + $x, $this->paddingTop + $y);
		$this->pdf->Cell($w, $this->lineHeight, $content, $this->cellBorder);
	}

	/**
	 * Draw cell with content
	 * @param float $x
	 * @param float $y
	 * @param float $w
	 * @param string $date
	 */
	protected function drawDateCell($x, $y, $w, $date)
	{
		$dateObject = new \DateTime($date);

		$this->pdf->SetXY($this->paddingLeft + $x, $this->paddingTop + $y);
		$this->pdf->Cell($w, $this->lineHeight, $dateObject->format('m'), $this->cellBorder);
		
		$x += 8;
		$this->pdf->SetXY($this->paddingLeft + $x, $this->paddingTop + $y);
		$this->pdf->Cell($w, $this->lineHeight, $dateObject->format('d'), $this->cellBorder);

		$x += 7;
		$this->pdf->SetXY($this->paddingLeft + $x, $this->paddingTop + $y);
		$this->pdf->Cell($w, $this->lineHeight, $dateObject->format('Y'), $this->cellBorder);
	}

	/**
	 * Draw cell with content
	 * @param float $x
	 * @param float $y
	 * @param float $w
	 * @param string $date
	 */
	protected function drawBillDateCell($x, $y, $w, $date)
	{
		$dateObject = new \DateTime($date);

		$this->pdf->SetXY($this->paddingLeft + $x, $this->paddingTop + $y);
		$this->pdf->Cell($w, $this->lineHeight, $dateObject->format('m'), $this->cellBorder);

		$x += 7;
		$this->pdf->SetXY($this->paddingLeft + $x, $this->paddingTop + $y);
		$this->pdf->Cell($w, $this->lineHeight, $dateObject->format('d'), $this->cellBorder);

		$x += 7;
		$this->pdf->SetXY($this->paddingLeft + $x, $this->paddingTop + $y);
		$this->pdf->Cell($w, $this->lineHeight, $dateObject->format('y'), $this->cellBorder);
	}

	/**
	 * Draw cell with content
	 * @param float $x
	 * @param float $y
	 * @param float $w
	 * @param string $phone
	 */
	protected function drawPhoneCell($x, $y, $w, $phone)
	{

		$this->pdf->SetXY($this->paddingLeft + $x, $this->paddingTop + $y);
		$this->pdf->Cell($w, $this->lineHeight, substr($phone, 0, 3), $this->cellBorder);

		$x += 9;
		$this->pdf->SetXY($this->paddingLeft + $x, $this->paddingTop + $y);
		$this->pdf->Cell($w, $this->lineHeight, substr($phone, 3), $this->cellBorder);
	}

	/**
	 * Draw cell with content
	 * @param float $x
	 * @param float $y
	 * @param float $w
	 * @param string $price
	 */
	protected function drawPriceCell($x, $y, $w, $price)
	{

		$this->pdf->SetXY($this->paddingLeft + $x, $this->paddingTop + $y);
		$this->pdf->Cell($w, $this->lineHeight, floor($price), 0, 3, $this->cellBorder);

		$x += 5;
		$this->pdf->SetXY($this->paddingLeft + $x, $this->paddingTop + $y);
		$this->pdf->Cell($w, $this->lineHeight, substr($this->formatPrice($price - floor($price)), 2, 2), 3, $this->cellBorder);
	}

	/**
	 * Return formatted name
	 * @param \Opake\Model\Cases\Registration|\Opake\Model\Insurance\Data\Regular $object
	 * @return string
	 */
	protected function getFullName($object)
	{
		$lastName = $object->last_name;
		$firstName = $object->first_name;
		if ($object->middle_name) {
			$firstName .= ', ' . $object->middle_name;
		}
		return $lastName . ', ' . $firstName;
	}

	protected function clearAddress($address)
	{
		return preg_replace('/[^\w\s]/u', '', $address);
	}

	protected function formatPrice($price)
	{
		return number_format($price, 2, '.', '');
	}

	/**
	 * @return string
	 */
	public function compile()
	{
		$this->pdf->AddPage('P', 'letter');
		$this->pdf->SetXY(0, 0);
		$this->pdf->SetMargins(0, 0);
		$this->pdf->SetAutoPageBreak(false);
		$this->pdf->SetFont($this->font, '', $this->fontSize);

		if ($this->useTemplate) {
			$this->pdf->Image($this->pixie->app_dir . 'docs/CMS1500_2.jpg', 0, 0, $this->pdf->GetPageWidth(), $this->pdf->GetPageHeight());
		}

		// Data objects
		$case = $this->case;
		$site = $case->location->site;
		$organization = $case->organization;
		$registration = $case->registration;
		$firstSurgeon = $case->getFirstSurgeon();

		$coding = $case->coding;

		$primaryInsurance = $coding->getPrimaryInsurance();
		$secondaryInsurance = $coding->getSecondaryInsurance();


		if ($coding->insurance_order) {
			$mainInsurance = $coding->getAssignedInsurance();
		} else {
			$mainInsurance = $primaryInsurance;
		}

		$codingBills = $coding->bills->limit(6)->find_all();
		$codingBillsChargesSum = 0;
		foreach ($coding->bills->find_all() as $bill) {
			$chargesSum = ($bill->quantity !== '' && $bill->quantity !== null) ? ($bill->charge * $bill->quantity) : $bill->charge;
			$codingBillsChargesSum += $chargesSum;
		}

		$fullPatientName = $this->getFullName($registration);

		$insuredName = null;
		$secondaryInsuredName = null;

		if ($mainInsurance) {
			if (isset($mainInsurance->getCaseInsuranceDataModel()->last_name)) {
				$insuredName = $this->getFullName($mainInsurance->getCaseInsuranceDataModel());
			}
			if (isset($mainInsurance->getCaseInsuranceDataModel()->last_name)) {
				$secondaryInsuredName = $this->getFullName($mainInsurance->getCaseInsuranceDataModel());
			}
		}

		// 1. Carrier

		if ($mainInsurance) {
			$this->drawCell(97, -2, 5, $mainInsurance->getInsuranceCompanyName());

			$insAddress = $this->clearAddress($mainInsurance->getAddress());
			$this->drawCell(97, 1, 5, $insAddress);

			$parts = [];
			if ($mainInsurance->getCity()) {
				$parts[] = $mainInsurance->getCity()->name;
			}
			if ($mainInsurance->getState()) {
				$parts[] = $mainInsurance->getState()->code;
			}
			if ($mainInsurance->getZipCode()) {
				$parts[] = $mainInsurance->getZipCode();
			}

			$this->drawCell(97, 4, 5, implode(' ', $parts));
		}

		// 2. Patient and insured information

		// Insurance type
		if ($mainInsurance && $mainInsurance->getCaseInsurance()->type) {
			if ($mainInsurance->getCaseInsurance()->type == AbstractType::INSURANCE_TYPE_MEDICARE) {
				$this->drawCell(1.4, 18, 5, 'X');
			} elseif ($mainInsurance->getCaseInsurance()->type == AbstractType::INSURANCE_TYPE_MEDICAID) {
				$this->drawCell(18.5, 18, 5, 'X');
			} elseif ($mainInsurance->getCaseInsurance()->type == AbstractType::INSURANCE_TYPE_TRICARE) {
				$this->drawCell(36.3, 18, 5, 'X');
			} elseif ($mainInsurance->getCaseInsurance()->type == AbstractType::INSURANCE_TYPE_CHAMPVA) {
				$this->drawCell(59, 18, 5, 'X');
			} elseif ($mainInsurance->getCaseInsurance()->type == AbstractType::INSURANCE_TYPE_COMMERCIAL) {
				$this->drawCell(77, 18, 5, 'X');
			} elseif ($mainInsurance->getCaseInsurance()->type == AbstractType::INSURANCE_TYPE_FECA_BLACK_LUNG) {
				$this->drawCell(97, 18, 5, 'X');
			} else {
				$this->drawCell(112.5, 18, 5, 'X');
			}
		}

		// Insurance policy #
		if ($mainInsurance) {
			if ($mainInsurance->getCaseInsurance()->isWorkersCompanyInsurance()) {
				$this->drawCell(127, 18, 5, $mainInsurance->getCaseInsuranceDataModel()->claim);
			} else if ($mainInsurance->getCaseInsurance()->isAutoAccidentInsurance()) {
				$this->drawCell(127, 18, 5, $mainInsurance->getCaseInsuranceDataModel()->claim);
			} else {
				if (isset($mainInsurance->getCaseInsuranceDataModel()->policy_number)) {
					$this->drawCell(127, 18, 5, $mainInsurance->getCaseInsuranceDataModel()->policy_number);
				}
			}
		}

		// Patient name, bday, sex
		$this->drawCell(2.5, 26, 5, $fullPatientName);
		$this->drawDateCell(78, 26.8, 5, $registration->dob);
		if ($registration->gender == Patient::GENDER_MALE) {
			$this->drawCell(105, 26.5, 5, 'X');
		} elseif ($registration->gender == Patient::GENDER_FEMALE) {
			$this->drawCell(117.5, 26.5, 5, 'X');
		}

		// Insured name, relationship, address, city, state, zip, phone
		if ($mainInsurance) {
			if ($mainInsurance->getCaseInsurance()->isWorkersCompanyInsurance()) {
				$this->drawCell(126.5, 26, 5, $mainInsurance->getCaseInsuranceDataModel()->employer_name);
				$this->drawCell(126.5, 34.5, 5, $this->clearAddress($mainInsurance->getCaseInsuranceDataModel()->employer_address));
				$this->drawCell(126.5, 43, 5, $mainInsurance->getCaseInsuranceDataModel()->employer_city->name);
				$this->drawCell(187, 43, 5, $mainInsurance->getCaseInsuranceDataModel()->employer_state->code);
				$this->drawCell(126.5, 51.5, 5, $mainInsurance->getCaseInsuranceDataModel()->employer_zip);
			} else if ($mainInsurance->getCaseInsurance()->isAutoAccidentInsurance()) {
				$this->drawCell(126.5, 26, 5, $fullPatientName);
				$this->drawCell(126.5, 34.5, 5, $this->clearAddress($registration->home_address));
				$this->drawCell(126.5, 43, 5, $registration->home_city->name);
				$this->drawCell(187, 43, 5, $registration->home_state->code);
				$this->drawCell(126.5, 51.5, 5, $registration->home_zip_code);
				$this->drawPhoneCell(165, 52, 5, $registration->home_phone);
			} else {
				if ($mainInsurance && isset($mainInsurance->getCaseInsuranceDataModel()->relationship_to_insured)) {
					if ($mainInsurance->getCaseInsuranceDataModel()->relationship_to_insured == Registration::RELATIONSHIP_TO_INSURED_SELF) {
						$this->drawCell(126.5, 26, 5, $fullPatientName);
						$this->drawCell(126.5, 34.5, 5, $this->clearAddress($registration->home_address));
						$this->drawCell(126.5, 43, 5, $registration->home_city->name);
						$this->drawCell(187, 43, 5, $registration->home_state->code);
						$this->drawCell(126.5, 51.5, 5, $registration->home_zip_code);
						$this->drawPhoneCell(165, 52, 5, $registration->home_phone);
					} else {
						$this->drawCell(126.5, 26, 5, $insuredName);
						$this->drawCell(126.5, 34.5, 5, $this->clearAddress($mainInsurance->getCaseInsuranceDataModel()->address));
						$this->drawCell(126.5, 43, 5, $mainInsurance->getCaseInsuranceDataModel()->city->name);
						$this->drawCell(187, 43, 5, $mainInsurance->getCaseInsuranceDataModel()->state->code);
						$this->drawCell(126.5, 51.5, 5, $mainInsurance->getCaseInsuranceDataModel()->zip_code);
						$this->drawPhoneCell(165, 52, 5, $mainInsurance->getCaseInsuranceDataModel()->phone);
					}

					if ($mainInsurance->getCaseInsuranceDataModel()->relationship_to_insured == Registration::RELATIONSHIP_TO_INSURED_SELF) {
						$this->drawCell(82, 35, 5, 'X');
					} elseif (($mainInsurance->getCaseInsuranceDataModel()->relationship_to_insured == Registration::RELATIONSHIP_TO_INSURED_SPOUSE)
						|| ($mainInsurance->getCaseInsuranceDataModel()->relationship_to_insured == Registration::RELATIONSHIP_TO_INSURED_LIFE_PARTNER)
					) {
						$this->drawCell(95, 35, 5, 'X');
					} else {
						$this->drawCell(117.7, 35, 5, 'X');
					}
				}
			}


			// Other claim ID
			if ($mainInsurance->getCaseInsurance()->isWorkersCompanyInsurance() || $mainInsurance->getCaseInsurance()->isAutoAccidentInsurance()) {
				$this->drawCell(126, 77, 5, 'Y4');
				$this->drawCell(131.5, 77, 5, substr($mainInsurance->getCaseInsuranceDataModel()->claim, 0, 28));
			}
		}

		// Patient address, city, state, zip, phone
		$this->drawCell(2.5, 34.5, 5, $this->clearAddress($registration->home_address));
		$this->drawCell(2.5, 43, 5, $registration->home_city->name);
		$this->drawCell(66, 43, 5, $registration->home_state->code);
		$this->drawCell(2.5, 51.5, 5, $registration->home_zip_code);
		$this->drawPhoneCell(38, 52, 5, $registration->home_phone);

		// Secondary Insured name
		if ($secondaryInsurance && isset($secondaryInsurance->getCaseInsuranceDataModel()->relationship_to_insured)) {
			if ($secondaryInsurance->getCaseInsuranceDataModel()->relationship_to_insured == Registration::RELATIONSHIP_TO_INSURED_SELF) {
				$this->drawCell(2.5, 60, 5, $fullPatientName);
			} else {
				$this->drawCell(2.5, 60, 5, $secondaryInsuredName);
			}
			$this->drawCell(2.5, 68, 5, $secondaryInsurance->getCaseInsuranceDataModel()->policy_number);
			$this->drawCell(2.5, 94, 5, $secondaryInsurance->getCaseInsuranceDataModel()->insurance->name);
		}

		// Is patient's condition related to
		if ($mainInsurance) {
			if ($mainInsurance->getCaseInsurance()->type == AbstractType::INSURANCE_TYPE_WORKERS_COMP) {
				$this->drawCell(87.3, 69, 5, 'X');
				$this->drawCell(102.5, 77.7, 5, 'X');
				$this->drawCell(102.5, 86, 5, 'X');
			} elseif ($mainInsurance->getCaseInsurance()->type == AbstractType::INSURANCE_TYPE_AUTO_ACCIDENT) {
				$this->drawCell(87.3, 77.7, 5, 'X');
				$this->drawCell(102.5, 69, 5, 'X');
				$this->drawCell(102.5, 86, 5, 'X');
				$this->drawCell(113, 77.7, 5, $mainInsurance->getCaseInsuranceDataModel()->state->code);
			} else {
				$this->drawCell(87.3, 86, 5, 'X');
				$this->drawCell(102.5, 69, 5, 'X');
				$this->drawCell(102.5, 77.7, 5, 'X');
			}
		}

		// Insurance policy #, Insured dob and sex, insurance company
		if ($mainInsurance && $mainInsurance->getCaseInsuranceDataModel()) {
			if (isset($mainInsurance->getCaseInsuranceDataModel()->group_number)) {
				$this->drawCell(127, 60, 5, $mainInsurance->getCaseInsuranceDataModel()->group_number);
			}

			if (isset($mainInsurance->getCaseInsuranceDataModel()->relationship_to_insured)) {
				if ($mainInsurance->getCaseInsuranceDataModel()->relationship_to_insured == Registration::RELATIONSHIP_TO_INSURED_SELF) {
					$this->drawDateCell(134, 69.5, 5, $registration->dob);
					if ($registration->gender == Patient::GENDER_MALE) {
						$this->drawCell(171, 69, 5, 'X');
					} elseif ($registration->gender == Patient::GENDER_FEMALE) {
						$this->drawCell(189, 69, 5, 'X');
					}
				} else {
					$this->drawDateCell(134, 69.5, 5, $mainInsurance->getCaseInsuranceDataModel()->dob);
					if ($mainInsurance->getCaseInsuranceDataModel()->gender == Patient::GENDER_MALE) {
						$this->drawCell(171, 69, 5, 'X');
					} elseif ($mainInsurance->getCaseInsuranceDataModel()->gender == Patient::GENDER_FEMALE) {
						$this->drawCell(189, 69, 5, 'X');
					}
				}
			}

			if (isset($mainInsurance->getCaseInsuranceDataModel()->insurance)) {
				$this->drawCell(127, 85.5, 5, $mainInsurance->getCaseInsuranceDataModel()->insurance->name);
			}

			if(isset($mainInsurance->getCaseInsuranceDataModel()->authorization_or_referral_number)) {
				$this->drawCell(127, 154, 5, $mainInsurance->getCaseInsuranceDataModel()->authorization_or_referral_number);
			}

		}

		// Is another health benefit plan
		if ($secondaryInsurance) {
			$this->drawCell(130.2, 94.3, 5, 'X');
		} else {
			$this->drawCell(143, 94.3, 5, 'X');
		}

		// Patient's and insured's signatures
		$signature = 'No Signature on File';
		if ($coding->authorization_release_information_payment) {
			$signature = 'Signature on File';
		}
		$this->drawCell(14, 110.8, 5, $signature);
		$this->drawCell(140, 110.8, 5, $signature);

		$date = new \DateTime();
		$this->drawCell(90.5, 110.8, 5, TimeFormat::getDateWithLeadingZeros($date));

		// 3. Physician or supplier information

		// Date of current
		// 14. Date of current illness, injury, or pregnancy
		if ($mainInsurance->getCaseInsurance()->isWorkersCompanyInsurance() || $mainInsurance->getCaseInsurance()->isAutoAccidentInsurance()) {
			if ($case->date_of_injury) {
				$this->drawDateCell(4, 120, 5, $case->date_of_injury);
			}
		} else {
			$this->drawDateCell(4, 120, 5, $case->time_start);
		}

		$this->drawCell(39, 120, 5, '431');

		// Dates patient unable to work
		if ($case->is_unable_to_work) {
			$this->drawDateCell(136, 120, 5, $case->unable_to_work_from);
			$this->drawDateCell(171.5, 120, 5, $case->unable_to_work_to);
		}

		// Referring provider
		if ($case->point_of_origin == 2) {
			$this->drawCell(2.5, 127.4, 5, 'DN');
			$this->drawCell(9, 127.4, 5, $case->referring_provider_name);
			$this->drawCell(81, 128.4, 5, $case->referring_provider_npi);
		}

		if (empty($case->referring_provider_npi)) {
			$this->drawCell(81, 128.4, 5, $firstSurgeon->credentials->npi_number);
		}

		// Additional claim info
		$this->drawCell(2.5, 134.5, 5, substr($coding->addition_claim_information, 0, 48));
		$this->drawCell(2.5, 137.5, 5, substr($coding->addition_claim_information, 48, 23));

		// Outside lab, charges
		if ($coding->has_lab_services_outside) {
			$this->drawCell(130.5, 136.5, 5, 'X');
			$this->drawCell(164, 137, 5, $this->formatPrice($coding->lab_services_outside_amount));
		} else {
			$this->drawCell(143.2, 136.5, 5, 'X');
		}
		
		// Diagnosis or Nature of Illness or Injury
		$this->drawCell(106, 142, 5, '0');

		$strNumber = 0;
		$x = 6;
		$y = 145.5;
		foreach ($coding->diagnoses->find_all() as $diagnosis) {
			$this->drawCell($x, $y, 5, $diagnosis->icd->code);
			$y += 4.3;
			$strNumber++;
			if ($strNumber == 3) {
				$strNumber = 0;
				$y = 145.5;
				$x += 33.2;
			}
		}

		// Medicaid resubmission
		if ($coding->bill_type == Coding::BILL_TYPE_REPLACEMENT_OF_PRIOR_CLAIM) {
			$this->drawCell(127, 145.5, 5, '7');
			$this->drawCell(155, 145.5, 5, $coding->original_claim_id);
		} elseif ($coding->bill_type == Coding::BILL_TYPE_CANCEL_OF_PRIOR_CLAIM) {
			$this->drawCell(127, 145.5, 5, '8');
			$this->drawCell(155, 145.5, 5, $coding->reference_number);
		}

		// Table of HCPCS codes
		$y = 170.5;
		foreach ($codingBills as $bill) {
			$this->drawBillDateCell(2, $y, 5, $case->time_start);
			$this->drawBillDateCell(24.5, $y, 5, $case->time_start);
			$this->drawCell(47, $y, 5, '24');

			$chargeMasterEntry = $bill->getChargeMasterEntry();
			if ($chargeMasterEntry) {
				$this->drawCell(63, $y, 5, substr($chargeMasterEntry->cpt, 0, 6));
				$mods = $this->getModifiers($bill);
				if (isset($mods[0])) {
					$this->drawCell(81, $y, 5, $mods[0]);
				}
				if (isset($mods[1])) {
					$this->drawCell(89, $y, 5, $mods[1]);
				}
				if (isset($mods[2])) {
					$this->drawCell(97, $y, 5, $mods[2]);
				}
				if (isset($mods[3])) {
					$this->drawCell(105, $y, 5, $mods[3]);
				}

			}

			$diagnoses = $bill->getDiagnosesLetters();
			if ($diagnoses) {
				$this->drawCell(113, $y, 5, implode('', $diagnoses));
			}
			$chargesSum = ($bill->quantity !== '' && $bill->quantity !== null) ? ($bill->charge * $bill->quantity) : $bill->charge;
			$this->drawPriceCell(136, $y, 5, $chargesSum);
			if ($bill->quantity) {
				$this->drawCell(148, $y, 5, $bill->quantity);
			}
			$this->drawCell(171, $y, 5, substr($site->npi, 0, 10));

			$y += 8.5;
		}
		// Federal tax id number
		$this->drawCell(1.5, 221, 5, $firstSurgeon->credentials->tin);
		$this->drawCell(47, 221, 5, 'X');

		// Patient's account number
		$this->drawCell(58, 220.5, 5, $case->id);

		// Accept assignment
		$this->drawCell(95, 221, 5, 'X');

		// Total charge, amount paid
		$this->drawPriceCell(141.5, 221.5, 5, $codingBillsChargesSum);

		if($coding->insurance_order && $coding->insurance_order != 1) {
			$amountPaid = $coding->amount_paid_by_other_insurance;
		} else {
			$amountPaid = $coding->amount_paid;
		}
		$this->drawPriceCell(166.7, 221.5, 5, $amountPaid);

		//31. Signature of physician
		$this->drawCell(2, 240.0, 5, 'Signature on File');
		$currentDate = new \DateTime();
		$this->drawCell(36, 240.0, 5, $currentDate->format('m/d/Y'));
		$this->pdf->setFontSize($this->fontSize);

		// Service facility location information
		$this->drawCell(57, 229, 5, substr($site->name, 0, 26));
		$this->drawCell(57, 233, 5, substr($this->clearAddress($site->address), 0, 26));
		$this->drawCell(57, 237, 5, substr($site->city->name . ' ' . $site->state->code . ' ' . $site->zip_code, 0, 29));

		// Billing provider info
		$this->drawCell(126, 229, 5, substr($site->pay_name, 0, 29));
		$this->drawCell(168, 226, 5, substr(substr($site->contact_phone, 0, 3) . '   ' . substr($site->contact_phone, 3, 3) . '-' . substr($site->contact_phone, 6, 4), 0, 29));
		$this->drawCell(126, 233, 5, substr($this->clearAddress($site->pay_address), 0, 29));
		$this->drawCell(126, 237, 5, substr($site->pay_city->name . ' ' . $site->pay_state->code . ' ' . $site->pay_zip_code, 0, 29));
		$this->drawCell(128, 242, 5, substr($site->npi, 0, 10));

		return $this->pdf->Output('S');
	}

	protected function getModifiers($bill)
	{
		$mods = [];

		$chargeMasterEntry = $bill->getChargeMasterEntry();
		if ($chargeMasterEntry) {
			if ($bill->custom_modifier) {
				$items = explode(',', $bill->custom_modifier);
				foreach ($items as $item) {
					$item = trim($item);
					if (!empty($item)) {
						$mods[] = substr($item, 0, 2);
					}
				}
			} else {
				$mods[] = substr($chargeMasterEntry->cpt_modifier1, 0, 2);
				$mods[] = substr($chargeMasterEntry->cpt_modifier2, 0, 2);
			}
		}

		return $mods;
	}
}
