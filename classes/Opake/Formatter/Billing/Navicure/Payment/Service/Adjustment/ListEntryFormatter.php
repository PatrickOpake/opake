<?php

namespace Opake\Formatter\Billing\Navicure\Payment\Service\Adjustment;

use Opake\Formatter\Billing\Navicure\Payment\BasePaymentFormatter;

class ListEntryFormatter extends BasePaymentFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
			    'type_name',
				'amount',
			    'quantity',
			    'reason_code',
			    'reason_code_description'
			],
			'fieldMethods' => [
				'id' => 'int',
			    'type_name' => 'typeName',
			    'amount' => 'money',
			    'reason_code_description' => 'reasonCodeDescription'
			]
		]);
	}

	protected function formatTypeName($name, $options, $model)
	{
		$typesList = \Opake\Model\Billing\Navicure\Payment\Service\Adjustment::getTypesDescriptionList();
		return (isset($typesList[$model->type])) ? $typesList[$model->type] : '';
	}

	protected function formatReasonCodeDescription($name, $options, $model)
	{
		$descList = \Opake\Model\Billing\Navicure\Payment\Service\Adjustment::getReasonCodesDescriptionList();
		return (isset($descList[$model->reason_code])) ? $descList[$model->reason_code] : '';
	}
}