<?php

namespace Opake\ActivityLogger\Comparer;

class PrefCardComparer extends ChildModelsChangesComparer
{
	/**
	 * @return array
	 */
	protected function getChildModelFields()
	{
		return [
			'notes' => [
				'fieldsForCompare' => [
					'text'
				]
			],
			'items' => [
				'fieldsForCompare' => [
					'inventory_id',
					'quantity'
				]
			]
		];
	}
}
