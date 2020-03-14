<?php

namespace OpakeAdmin\Service\ASCX12\E837I\Request\Blocks;

use Opake\Helper\TimeFormat;

class ServicesInfo extends \OpakeAdmin\Service\ASCX12\E837\Request\Blocks\ServicesInfo
{
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

				if (!$bill->revenue_code) {
					throw new \Exception('Revenue code for each procedure is required for the Electronic UB04 claim');
				}

				$data[] = [
					'LX',
					$index + 1
				];

				$fee = $chargeMasterEntry->getFeeScheduleEntry();

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

				$chargesSum = ($quantity !== '' && $quantity !== null) ? ($bill->charge * $quantity) : $bill->charge;

				$data[] = [
					'SV2',
					$bill->revenue_code,
					$this->mergeComponents($serviceCode),
					round($chargesSum, 2),
					'UN', //MJ for Anasthesia
					$quantity ? : '0',
				    '',
					($fee && $fee->non_covered_charges) ? $fee->non_covered_charges : null
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