<?php

namespace Opake\Formatter\Cases\Item;

use Opake\Model\Card\Staff as CardStaff;

class CardListFormatter extends ItemFormatter
{

	/**
	 * @return array
	 */
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
				'time_start',
				'first_surgeon_for_dashboard',
				'patient',
				'type',
				'card_status'
			],
			'fieldMethods' => [
				'type' => ['relationshipOne', [
						'formatter' => [
							'class' => '\Opake\Formatter\BaseDataFormatter',
							'fields' => [
								'full_name'
							],
							'fieldMethods' => [
								'full_name' => ['modelMethod', [
										'modelMethod' => 'getFullName'
									]
								]
							]
						]
					]
				],
				'patient' => ['patient', [
						'formatter' => [
							'class' => '\Opake\Formatter\BaseDataFormatter',
							'fields' => [
								'first_name',
								'last_name',
								'full_mrn'
							],
							'fieldMethods' => [
								'full_mrn' => ['modelMethod', [
										'modelMethod' => 'getFullMrn'
									]
								]
							]
						]
					]
				],
				'card_status' => 'cardStatus'
			]
		]);
	}

	protected function formatCardStatus($name, $options, $model)
	{
		$card = $model->getCard();
		if ($card->loaded()) {
			return $card->status;
		}
		return CardStaff::STATUS_OPEN;
	}

}
