<?php

namespace Opake\Formatter\BookingSheetTemplate;

use Opake\Formatter\BaseDataFormatter;

class ListFormatter extends BaseDataFormatter
{
	public function getDefaultConfig()
	{
		return $this->mergeConfigs(parent::getDefaultConfig(), [
			'fields' => [
				'id',
			    'organization_id',
				'type',
			    'name',
			    'is_all_sites',
			    'sites'
			],
			'fieldMethods' => [
				'id' => 'int',
			    'organization_id' => 'int',
			    'type' => 'int',
			    'is_all_sites' => 'bool',
			    'sites' => ['relationshipMany', [
				    'formatter' => [
					    'class' => '\Opake\Formatter\BaseDataFormatter',
					    'fields' => [
						    'id',
					        'name'
					    ],
					    'fieldMethods' => [
						    'id' => 'int'
					    ]
				    ]
			    ]]
			]
		]);
	}
}