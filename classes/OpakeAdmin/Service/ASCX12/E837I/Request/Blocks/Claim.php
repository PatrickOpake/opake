<?php

namespace OpakeAdmin\Service\ASCX12\E837I\Request\Blocks;

use Opake\Helper\TimeFormat;

class Claim extends \OpakeAdmin\Service\ASCX12\E837\Request\Blocks\Claim
{
	protected function generateSegmentsBeforeChildren($data)
	{

		$claimIdString = str_pad($this->claimEntry->id(), 4, '0', STR_PAD_LEFT);
		$case = $this->case;

		$codingBillsChargesSum = $this->billsContainer->getTotalSum();;
		$codingBillsChargesSum = round($codingBillsChargesSum, 2);

		$claimFrequencyCode = (!$this->originalClaim) ? '1' : '7';

		//Loop 2300 - Claim
		$claimData = [
			'CLM',
			$claimIdString,
			$codingBillsChargesSum,
			'',
			'',
			$this->mergeComponents([
				$this->getPlaceOfServiceOrFacilityCode(),
				$this->getCodeQualifier(),
				$claimFrequencyCode
			])
		];

		$data[] = array_merge($claimData, $this->getAdditionalCodes());

		$dos = new \DateTime($case->time_start);
		$dosStr = $dos->format('Ymd');

		$data[] = [
			'DTP',
		    '434',
		    'RD8',
		    $dosStr . '-' . $dosStr
		];

		$pointOfOrigin = null;
		if ($case->point_of_origin) {
			$pointOfOriginTypes = $this->getSourceOfAdmissionTypes();
			if (isset($pointOfOriginTypes[$case->point_of_origin])) {
				$pointOfOrigin = $pointOfOriginTypes[$case->point_of_origin];
			}
		}

		if (!$case->coding->discharge_code->loaded()) {
			throw new \Exception('Discharge is required for the Electronic UB04 claim');
		}

		$data[] = [
			'CL1',
		    $case->registration->admission_type,
		    $pointOfOrigin ? : '9', //9 is "Info is not avail"
			str_pad((string) $case->coding->discharge_code->code, 2, '0', STR_PAD_LEFT)
		];

		// REF - PAYER CLAIM CONTROL NUMBER
		if ($this->originalClaim) {
			$data[] = [
				'REF',
				'F8',
				$this->originalClaim
			];
		}

		$data[] = [
			'REF',
		    'D9',
		    $claimIdString
		];

		if ($mrn = $case->registration->patient->getFullMrn()) {
			$data[] = [
				'REF',
				'EA',
				$this->prepareNumber($mrn, 50)
			];
		}

		/*$amountPaid = (float) $case->coding->amount_paid;
		if ($amountPaid) {
			$data[] = [
				'AMT',
				'C4',
				number_format($amountPaid, 2)
			];
		}*/

		$data = $this->generateConditionalCodes($data);
		$data = $this->generateOccurrenceCodes($data);
		$data = $this->generateValueCodes($data);
		$data = $this->generateDiagnoses($data);

		return $data;
	}

	protected function generateConditionalCodes($data)
	{
		$coding = $this->case->coding;
		$conditionalCodes = $coding->condition_codes->find_all()->as_array();
		$conditionalCodes = array_slice($conditionalCodes, 0, 12);

		$conditionalData = [];
		foreach ($conditionalCodes as $conditionalCode) {
			if ($conditionalCode->code) {
				$code = substr($conditionalCode->code, 0 ,2);
				$conditionalData[] = $this->mergeComponents(['BG', $code]);
			}
		}

		if ($conditionalData) {
			$conditionalResult = [
				'HI'
			];
			foreach ($conditionalData as $component) {
				$conditionalResult[] = $component;
			}
			$data[] = $conditionalResult;
		}

		return $data;
	}

	protected function generateOccurrenceCodes($data)
	{
		$coding = $this->case->coding;
		$occurrences = $coding->occurrences->find_all()->as_array();
		$occurrences = array_slice($occurrences, 0, 12);

		$occurrencesData = [];
		foreach ($occurrences as $occurrence) {
			if ($occurrence->occurrence_code->loaded() && $occurrence->date) {
				$occurrenceCode = $occurrence->occurrence_code->code;
				$occurrenceCode = str_pad($occurrenceCode, 2, '0', STR_PAD_LEFT);
				$date = TimeFormat::fromDBDate($occurrence->date);
				$date = $date->format('Ymd');
				$occurrencesData[] = $this->mergeComponents(['BH', $occurrenceCode, 'D8', $date]);
			}
		}

		if ($occurrencesData) {
			$occurrencesResult = [
				'HI'
			];
			foreach ($occurrencesData as $component) {
				$occurrencesResult[] = $component;
			}
			$data[] = $occurrencesResult;
		}

		return $data;
	}

	protected function generateValueCodes($data)
	{
		$coding = $this->case->coding;
		$app = \Opake\Application::get();

		$valuesData = [];
		$values = $coding->values->with('value_code')
			->order_by($app->db->expr('ISNULL(value_code.code)'))
			->order_by('value_code.code')
			->find_all()
			->as_array();

		$values = array_slice($values, 0, 12);
		foreach ($values as $value) {
			if ($value->value_code->loaded() && $value->value_code->code) {
				$valuesData[] = $this->mergeComponents(['BE', $value->value_code->code, '', '', $value->amount]);
			}
		}

		if ($valuesData) {
			$valuesResult = [
				'HI'
			];
			foreach ($valuesData as $component) {
				$valuesResult[] = $component;
			}
			$data[] = $valuesResult;
		}

		return $data;
	}

	protected function generateDiagnoses($data)
	{
		$case = $this->case;

		//up to 12 diagnosis
		$diagnosisList = $case->coding->diagnoses
			->limit(12)
			->find_all()
			->as_array();

		$icdCodes = [];
		foreach ($diagnosisList as $index => $diagnosis) {
			$type = ($index == 0) ? 'ABK' : 'ABF';
			$icdCodes[] = $this->mergeComponents([$type, $this->prepareIcdCode($diagnosis->icd->code)]);
		}

		$data[] = array_merge([
			'HI'
		], $icdCodes);

		return $data;
	}

	protected function getPlaceOfServiceOrFacilityCode()
	{
		return '83'; //Ambulatory Surgical Center
	}

	protected function getCodeQualifier()
	{
		return 'A';
	}

	protected function getAdditionalCodes()
	{
		return ['', 'A', 'Y', 'Y'];
	}

	protected function getSourceOfAdmissionTypes()
	{
		return [
			\Opake\Model\Cases\Item::POINT_OF_ORIGIN_NON_HEALTH => '1',
			\Opake\Model\Cases\Item::POINT_OF_ORIGIN_NON_CLINIC => '2',
			\Opake\Model\Cases\Item::POINT_OF_ORIGIN_NON_HOSPITAL => '4',
			\Opake\Model\Cases\Item::POINT_OF_ORIGIN_NON_SNF => '5',
			\Opake\Model\Cases\Item::POINT_OF_ORIGIN_NON_FACILITY => '6',
			\Opake\Model\Cases\Item::POINT_OF_ORIGIN_NON_COURT => '8',
			\Opake\Model\Cases\Item::POINT_OF_ORIGIN_NON_INFO_NOT_AVAIL => '9',
			\Opake\Model\Cases\Item::POINT_OF_ORIGIN_NON_TRANSFER_SAME_HOSPITAL => 'D',
			\Opake\Model\Cases\Item::POINT_OF_ORIGIN_NON_AMBULATORY => 'E',
			\Opake\Model\Cases\Item::POINT_OF_ORIGIN_NON_HOSPICE_FACILITY => 'F'
		];
	}
}