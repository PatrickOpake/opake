<?php

use Opake\Model\Role;

return [
	'schedule' => [
		'title' => 'Schedule',
		'internal' => false,
		'permission' => ['schedule', 'index'],
		'access' => [Role::FullAdmin, Role::FullClinical, Role::Doctor, Role::SatelliteOffice, Role::Biller, Role::Scheduler],
		'items' => [
			'dashboard' => [
				'title' => 'Overview',
				'url' => '/overview/dashboard/%s',
				'permission' => ['dashboard', 'index'],
			],
			'cases' => [
				'title' => 'Calendar',
				'url' => '/cases/%s/',
				'permission' => ['cases', 'index'],
			],
			'booking' => [
				'title' => 'Booking Queue',
				'url' => '/booking/%s/',
				'permission' => ['booking', 'index'],
				'top_counter' => true
			],
			'canceled-cases' => [
				'title' => 'Canceled Cases',
				'url' => '/cases/%s/canceled/',
				'permission' => ['cancellation', 'index']
			],
			'verification' => [
				'title' => 'Verification & Pre-Authorization',
				'url' => '/verification/%s/',
				'permission' => ['verifications', 'index']
			],
		]
	],

	'clinicals' => [
		'title' => 'Clinicals',
		'callback' => 'check_access_clinical_item',
		'items' => [
			'registration' => [
				'title' => 'Registration',
				'url' => '/cases/registrations/%s/',
				'permission' => ['registration', 'index'],
				'internal' => false,
			],
			'pre-op' => [
				'title' => 'Pre-Op',
				'url' => '/cases/pre-op/%s/',
				'permission' => ['registration', 'index'],
			],
			'operative-reports' => [
				'title' => 'Op',
				'url' => '/operative-reports/my/%s',
				'permission' => ['operative_reports', 'index']
			],
			'post-op' => [
				'title' => 'Post-Op',
				'url' => '/cases/post-op/%s/',
				'permission' => ['registration', 'index'],
			],
			'discharge' => [
				'title' => 'Discharge',
				'url' => '/cases/discharge/%s/',
				'permission' => ['registration', 'index'],
			]
		]
	],

	'billing' => [
		'title' => 'Billing',
		'internal' => false,
		'access' => [Role::FullAdmin, Role::Biller, Role::Doctor],
		'permission' => ['billing', 'index'],
		'items' => [
			'billing' => [
				'title' => 'Claims Generation',
				'url' => '/billings/%s/',
				'access' => [Role::FullAdmin, Role::Biller],
			],
			'claims-management' => [
				'title' => 'Claims Management',
				'access' => [Role::FullAdmin, Role::Biller],
				'items' => [
					'electronic-claims' => [
						'title' => 'Electronic Claims',
						'url' => '/billings/claims-management/%s',
					],
					'paper-claims' => [
						'title' => 'Paper Claims',
						'url' => '/billings/claims-management/%s/paperClaims',
					]
				]
			],
			'claims-processing' => [
				'title' => 'Claims Processing',
			    'url' => '/billings/claims-processing/%s',
				'access' => [Role::FullAdmin, Role::Biller],
			],
			'ar-management' => [
				'title' => 'Accounts Receivable',
				'access' => [Role::FullAdmin, Role::Biller, Role::Doctor],
				'items' => [
					'collections' => [
						'title' => 'AR Management',
						'url' => '/billings/collections/%s/',
						'access' => [Role::FullAdmin, Role::Biller, Role::Doctor],
					],
					'eob_management' => [
						'title' => 'EOB Management',
						'url' => '/billings/eob-management/%s/',
						'access' => [Role::FullAdmin, Role::Biller],
					],
				]
			],
			'batch-eligibility' => [
				'title' => 'Batch Eligibility',
				'url' => '/billings/batch-eligibility/%s/',
				'access' => [Role::FullAdmin, Role::Biller],
			],
		    'ledger' => [
			    'title' => 'Payments and Statements',
			    'access' => [Role::FullAdmin, Role::Biller],
			    'items' => [
				    'ledger' => [
					    'title' => 'Posting and Ledger',
					    'url' => '/billings/ledger/%s/',
				    ],
				    'payment-activity' => [
					    'title' => 'Payment Activity',
					    'url' => '/billings/ledger-payment-activity/%s/',
				    ],
				    'patient-statement' => [
					    'title' => 'Patient Statement',
					    'url' => '/billings/patient-statement/%s/',
					    'access' => [Role::FullAdmin, Role::Biller],
				    ],
				    'itemized-bill' => [
					    'title' => 'Itemized Bill',
					    'url' => '/billings/itemized-bill/%s/',
					    'access' => [Role::FullAdmin, Role::Biller],
				    ],
				    'statement-history' => [
					    'title' => 'Statement History',
					    'url' => '/billings/ledger/%s/statementHistory',
				    ]
			    ]
		    ],

		]
	],

	'patients' => [
		'title' => 'Patients',
		'internal' => false,
		'access' => [Role::FullAdmin, Role::FullClinical, Role::Doctor, Role::SatelliteOffice, Role::Biller, Role::Scheduler],
		'permission' => ['patients', 'index'],
		'url' => '/patients/%s/',
		'items' => [
			/*'patients' => [
				'title' => 'Patients',
				'url' => '/patients/%s/',
			]*/
		]
	],

	'inventory' => [
		'title' => 'Inventory',
		'access' => [Role::FullAdmin, Role::FullClinical, Role::Doctor, Role::Biller, Role::Scheduler],
		'permission' => ['inventory', 'index'],
		'items' => [
			'inventory' => [
				'title' => 'Inventory',
				'url' => '/inventory/%s/',
			],
			'pref-cards' => [
				'title' => 'Preference Cards',
				'url' => '/cases/%s/cards/',
				'access' => [Role::FullAdmin, Role::FullClinical, Role::Scheduler]
			],
			'inventory-report' => [
				'title' => 'Reports',
				'url' => '/inventory-report/%s/',
			],
			'orders' => [
				'title' => 'Orders',
				'url' => '/orders/%s/',
				'access' => [Role::FullAdmin, Role::FullClinical, Role::Scheduler]
			],
			'invoices' => [
				'title' => 'Invoices',
				'url' => '/inventory/invoices/%s/',
				'access' => [Role::FullAdmin, Role::FullClinical, Role::Scheduler]
			],
		]
	],

	'analytics' => [
		'title' => 'Analytics',
		'items' => [
			'reports' => [
				'title' => 'Reports',
				'url' => '/analytics/reports/%s/',
				'access' => [Role::FullAdmin, Role::FullClinical, Role::Biller, Role::Scheduler],
				'permission' => ['analytics', 'view_reports'],
			],
			'credentials' => [
				'title' => 'Credentials',
				'access' => [Role::FullAdmin, Role::SatelliteOffice, Role::Biller, Role::Scheduler],
				'permission' => ['analytics', 'view_credentials'],
				'items' => [
					'medical' => [
						'title' => 'Med Staff',
						'url' => '/analytics/credentials/medical/%s/',
					],
					'non-surgical' => [
						'title' => 'Other Staff',
						'url' => '/analytics/credentials/non-surgical/%s/',
					]
				]
			],
			'user-activity' => [
				'title' => 'Audits',
				'access' => [Role::FullAdmin],
				'url' => '/analytics/%s/userActivity',
			],
		    'sms-log' => [
			    'title' => 'SMS Log',
		        'access' => [Role::FullAdmin],
		        'url' => '/analytics/sms-log/%s/'
		    ]
		]
	],

	'chat' => [
		'title' => 'Chat Log',
		'url' => '/chat/%s/',
		'permission' => ['chat', 'view_history']
	],

	'settings' => [
		'title' => 'Settings',
		'access' => [Role::FullAdmin, Role::FullClinical, Role::Doctor, Role::SatelliteOffice, Role::Biller, Role::Scheduler],
		'items' => [
			'organization' => [
				'title' => 'Organization',
				'access' => [Role::FullAdmin, Role::Biller],
				'items' => [
					'details' => [
						'title' => 'Details',
						'url' => '/clients/view/%s/',
						'access' => [Role::FullAdmin, Role::Biller],
					],
					'sites' => [
						'title' => 'Sites',
						'url' => '/clients/sites/%s/',
						'access' => [Role::FullAdmin, Role::Biller],
					],
					'users' => [
						'title' => 'Users',
						'url' => '/clients/users/%s/',
						'access' => [Role::FullAdmin, Role::Biller],
					],
				]
			],
			'databases' => [
				'title' => 'Databases',
				'access' => [Role::FullAdmin, Role::Biller],
				'items' => [
					'case_types' => [
						'title' => 'Procedures',
						'url' => '/settings/case-types/%s'
					],
					'inventory' => [
						'title' => 'Item Master',
						'url' => '/master/inventory/%s',
						'permission' => ['databases', 'view'],
					],
					'vendors' => [
						'title' => 'Vendors',
						'url' => '/vendors/%s',
						'permission' => ['databases', 'view'],
					],
					'inventory-multiplier' => [
						'title' => 'Item Multiplier',
						'url' => '/inventory-multiplier/%s',
						'permission' => ['inventory', 'index'],
					]
				]
			],
			'templates' => [
				'title' => 'Templates',
				'access' => [Role::FullAdmin, Role::FullClinical, Role::Doctor, Role::SatelliteOffice, Role::Biller, Role::Scheduler],
				'items' => [
					'booking-sheet' => [
						'title' => 'Booking Sheet',
					    'url' => '/settings/booking-sheet-templates/%s/',
					    'access' => [Role::FullAdmin, Role::Biller]
					],
					'site-template' => [
						'title' => 'Site Op Report',
						'url' => '/operative-reports/%s/siteTemplate/',
						'access' => [Role::FullAdmin, Role::Biller],
						'permission' => ['operative_reports', 'view']
					],
					'surgeon-templates' => [
						'title' => 'User Op Reports',
						'url' => '/operative-reports/%s/',
						'access' => [Role::FullAdmin, Role::SatelliteOffice, Role::Biller, Role::Scheduler],
						'permission' => ['operative_reports', 'view']
					],
					'operative-report' => [
						'title' => 'Operative Reports',
						'url' => '/operative-reports/%s/',
						'access' => [Role::FullClinical, Role::Doctor],
						'permission' => ['operative_reports', 'index']
					],
					'cards' => [
						'title' => 'Preference Cards',
						'url' => '/cards/%s/',
						'access' => [Role::FullAdmin, Role::FullClinical, Role::Doctor, Role::Biller, Role::Scheduler],
						'permission' => ['card', 'index']
					],
					'sms-template' => [
						'title' => 'SMS Template',
						'url' => '/sms-template/%s/',
						'access' => [Role::FullAdmin, Role::FullClinical, Role::Biller],
						'permission' => ['sms-template', 'index']
					],
				]
			],
			'forms' => [
				'title' => 'Charts',
				'access' => [Role::FullAdmin, Role::Biller],
				'items' => [
					'charts' => [
						'title' => 'Individual Charts',
						'url' => '/settings/forms/charts/%s/',
						'access' => [Role::FullAdmin, Role::Biller],
					],
					'chart-groups' => [
						'title' => 'Chart Groups',
						'url' => '/settings/forms/chart-groups/%s/',
						'access' => [Role::FullAdmin, Role::Biller],
					],
				]
			],
			'patient-portal' => [
				'title' => 'Patient Portal',
				'url' => '/settings/patient-portal/%s/',
				'access' => [Role::FullAdmin, Role::Biller],
				'callback' => 'check_access_patient_portal',
				'permission' => ['patient-portal', 'settings']
			],
			'alerts' => [
				'title' => 'Alerts',
				'url' => '/settings/alerts/%s/',
				'access' => [Role::FullAdmin, Role::Biller],
				'permission' => ['alerts', 'settings']
			],
		]
	],
	'profile' => [
		'access' => [Role::FullAdmin, Role::FullClinical, Role::Doctor, Role::SatelliteOffice, Role::Biller, Role::Scheduler],
		'items' => [
			'profile' => [
				'title' => 'Profile',
				'url' => '/profiles/%s/'
			],
			'credentials' => [
				'title' => 'Credentials',
				'url' => '/credentials/%s/'
			],
		]
	],
];
