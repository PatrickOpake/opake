<?php

namespace Opake\ActivityLogger\Comparer;


class CaseBillingComparer extends ChildModelsChangesComparer
{
	/**
	 * @return array
	 */
	protected function getChildModelFields()
	{

		return [
			'procedures' => [
				'comparerClass' => '\Opake\ActivityLogger\Comparer\CaseBillingProceduresComparer',
				'fieldsForCompare' => [
					'qty',
					'cpt_id',
					'cost',
					'date',
					'modifier1_id',
					'modifier2_id'
				]
			],
			'supplies' => [
				'comparerClass' => '\Opake\ActivityLogger\Comparer\CaseBillingProceduresComparer',
				'fieldsForCompare' => [
					'type_id',
					'qty',
					'hcpcs_id',
					'cost',
					'date',
					'modifier1_id',
					'modifier2_id',
				]
			],
			'occurences' => [
				'fieldsForCompare' => [
					'cond_id',
					'occ_id',
					'occurence_date'
				]
			],
			'notes' => [
				'fieldsForCompare' => [
					'note'
				]
			],
		];
	}
}