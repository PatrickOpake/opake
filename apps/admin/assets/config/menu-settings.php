<?php

return [
	'fields' => [
		'title' => 'Editable Fields',
		'items' => [
			'index' => [
				'title' => 'Practice',
				'model' => 'Practice',
				'url' => '/settings/fields/'
			],
			'departments' => [
				'title' => 'Department',
				'model' => 'Department',
				'url' => '/settings/fields/departments'
			],
			'item' => [
				'title' => 'Item Type',
				'model' => 'Inventory_Type',
				'url' => '/settings/fields/item'
			],
			'shipping' => [
				'title' => 'Shipping Type',
				'model' => 'Order_ShippingType',
				'url' => '/settings/fields/shipping'
			],
		]
	],
    'terms' => [
	    'title' => 'Terms of Service',
		'url' => '/settings/terms'
    ],
    'privacy' => [
	    'title' => 'Privacy Policy',
	    'url' => '/settings/privacy'
    ],
    'databases' => [
	    'title' => 'Databases',
	    'items' => [
		    'hcpc' => [
			    'title' => 'HCPCs',
			    'url' => '/settings/databases/hcpc'
		    ],
			'cpt' => [
				'title' => 'CPT<span class="registered-sign"></span> Codes',
				'model' => 'CPT',
				'url' => '/settings/databases/cpt'
			],
			'icd' => [
				'title' => 'ICD Codes',
				'url' => '/settings/databases/icd'
			],
		    'insurance-payors' => [
			    'title' => 'Insurances',
			    'url' => '/settings/databases/insurance-payors'
		    ],
		    'pref-card-stages' => [
			    'title' => 'Preference Card Stages',
			    'url' => '/settings/databases/pref-card-stages'
		    ],
		    'uom' => [
			    'title' => 'Units',
			    'url' => '/settings/databases/uom'
		    ]
	    ]
    ],
    'logs' => [
	    'title' => 'Logs',
        'items' => [
	        'navicure' => [
		        'title' => 'Navicure',
	            'url' => '/settings/logs/navicure'
	        ]
        ]
    ]
];