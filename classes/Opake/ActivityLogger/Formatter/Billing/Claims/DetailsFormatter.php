<?php

namespace Opake\ActivityLogger\Formatter\Billing\Claims;

use Opake\ActivityLogger\DefaultFormatter;
use Opake\ActivityLogger\LinkFormatterHelper;

class DetailsFormatter extends DefaultFormatter
{
	protected function formatValue($key, $value)
	{
		switch ($key) {

			case 'case':
				return LinkFormatterHelper::formatCaseLink($this->pixie, $value);
			case 'procedures':
				return ($value && is_array($value)) ? (implode(', ', $value)) : '';
		}

		return $value;
	}

	protected function prepareDataBeforeFormat($data)
	{
		if (isset($data['patient'])) {
			/** @var \Opake\Model\Patient $patient */
			$patient = $this->pixie->orm->get('Patient', $data['patient']);
			if ($patient->loaded()) {
				$data['patient_name'] = $patient->getFullNameForBooking();
				$data['patient_mrn'] = $patient->getFullMrn();
			}
			unset($data['patient']);
		}
		if (isset($data['claim_insurance'])) {
			/** @var \Opake\Model\Cases\Registration\Insurance $caseInsurance */
			$caseInsurance = $this->pixie->orm->get('Billing_Navicure_Claim_Insurance', $data['claim_insurance']);
			if ($caseInsurance->loaded()) {
				$data['insurance_company_name'] = $caseInsurance->getInsuranceName();
			}
			unset($data['claim_insurance']);
		}
		return $data;
	}

	protected function getIgnored()
	{
		return [
			'patient',
			'claim',
		];
	}

	protected function getLabels()
	{
		return [
			'case' => 'Case',
		    'patient_name' => 'Patient Name',
		    'patient_mrn' => 'MRN',
		    'insurance_company_name' => 'Insurance Company Name',
		    'total_amount' => 'Total Amount',
		    'procedures' => 'Procedure Codes'
		];
	}
}