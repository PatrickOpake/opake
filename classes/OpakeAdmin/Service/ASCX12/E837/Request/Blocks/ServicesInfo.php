<?php

namespace OpakeAdmin\Service\ASCX12\E837\Request\Blocks;

use Opake\Helper\TimeFormat;
use OpakeAdmin\Service\ASCX12\AbstractRequestSegment;
use OpakeAdmin\Service\Navicure\Claims\Procedures\ClaimProceduresContainer;

class ServicesInfo extends AbstractRequestSegment
{
	/**
	 * @var \Opake\Model\Cases\Item
	 */
	protected $case;

	/**
	 * @var ClaimProceduresContainer
	 */
	protected $billsContainer;

	/**
	 * ServicesInfo constructor.
	 *
	 * @param \Opake\Model\Cases\Item $case
	 */
	public function __construct(\Opake\Model\Cases\Item $case, $billsContainer)
	{
		$this->case = $case;
		$this->billsContainer = $billsContainer;
	}

	protected function generateSegmentsBeforeChildren($data)
	{
		$case = $this->case;

		$dateOfService = TimeFormat::fromDBDatetime($case->time_start);
		$dateOfService = $dateOfService->format('Ymd');

		//Loop 2400 - Service Line / pg. 354
		/** @var \Opake\Model\Cases\Coding\Bill $bill */
		foreach ($this->billsContainer->getBillsWithQuantities() as $index => $billWithQuantity) {
			list($bill, $quantity) = $billWithQuantity;
			$chargeMasterEntry = $bill->getChargeMasterEntry();
			if ($chargeMasterEntry) {
				$data[] = [
					'LX',
					$index + 1
				];

				$serviceCode = ['HC' , $this->prepareString($chargeMasterEntry->cpt, 48)];

				$modifiers = [];

				if ($bill->custom_modifier) {
					$parts = explode(',', $bill->custom_modifier);
					foreach ($parts as $mod) {
						$mod = trim($mod);
						if (!empty($mod)) {
							$mod = substr($mod, 0, 2);
							$modifiers[] = $mod;
						}
					}
				} else {
					if ($chargeMasterEntry->cpt_modifier1) {
						$modifiers[] = $chargeMasterEntry->cpt_modifier1;
					}
					if ($chargeMasterEntry->cpt_modifier2) {
						$modifiers[] = $chargeMasterEntry->cpt_modifier2;
					}
				}

				if ($modifiers) {
					$modifiers = array_map('trim', $modifiers);
					foreach ($modifiers as $mod) {
						$serviceCode[] = str_pad($this->prepareString($mod, 2), 2, ' ', STR_PAD_RIGHT);
					}
				}

				$diagnosisData = '1';
				if ($diagnoses = $bill->getDiagnoses()) {
					$diagnoses = array_slice($diagnoses, 0, 4);
					$rows = [];
					foreach ($diagnoses as $diag) {
						$rows[] = $diag->row;
					}
					$diagnosisData = $this->mergeComponents($rows);
				}

				$chargesSum = ($quantity !== '' && $quantity !== null) ? ($bill->charge * $quantity) : $bill->charge;

				$data[] = [
					'SV1',
					$this->mergeComponents($serviceCode),
					round($chargesSum, 2),
					'UN', //MJ for Anasthesia
					$quantity ? : '0',
					'',
					'',
					$diagnosisData
				];

				$data[] = [
					'DTP',
					'472',
					'D8',
					$dateOfService
				];
			}

		}

		return $data;
	}
}