<?php
namespace Opake\Formatter\Billing\Navicure\Claim\Coding;

use Opake\Formatter\BaseDataFormatter;
use Opake\Model\Billing\Navicure\Claim\StatusAcknowledgment;

class StatusAcknowledgmentFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'date',
				'amount',
				'status_text',
				'note'
			],
			'fieldMethods' => [
				'id' => 'int',
				'date' => 'toDate',
				'amount' => 'amount',
				'status_text' => 'statusText'
			]
		]);
	}

	protected function formatAmount($name, $options, $model)
	{
		return round($model->amount, 2);
	}

	protected function formatStatusText($name, $options, $model)
	{
		if ($model->status == StatusAcknowledgment::STATUS_ACCEPTED) {
			return 'Accepted';
		}

		if ($model->status == StatusAcknowledgment::STATUS_REJECTED) {
			return 'Rejected';
		}

		return '';
	}
}