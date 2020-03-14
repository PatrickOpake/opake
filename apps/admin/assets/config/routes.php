<?php

return array(
	'login' => array(
		'/auth(/<action>)',
		array(
			'controller' => 'auth',
			'action' => 'index'
		)
	),

	'booking/ajax/note' => array(
		'/booking/ajax/note/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Booking\Ajax\Note',
			'action' => 'list'
		),
	),
	'booking/ajax/charts' => array(
		'/booking/ajax/charts/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Booking\Ajax\Charts',
			'action' => 'list'
		),
	),

	'settings/booking-sheet-templates/ajax' => array(
		'/settings/booking-sheet-templates/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Settings\BookingSheetTemplate\Ajax',
			'action' => 'index'
		),
	),
    'settings/booking-sheet-templates' => array(
	    '/settings/booking-sheet-templates/<id>(/<action>(/<subid>))',
	    array(
		    'controller' => 'Settings\BookingSheetTemplate\BookingSheetTemplate',
		    'action' => 'index'
	    ),
    ),

	// Fee Schedule
	'clients/sites/fee-schedule/ajax' => array(
		'/clients/sites/ajax/<id>/fee-schedule(/<action>(/<subid>))',
		array(
			'controller' => 'FeeSchedule\Ajax',
			'action' => 'index'
		),
	),
	'clients/sites/fee-schedule' => array(
		'/clients/sites/<id>/fee-schedule(/<action>(/<subid>))',
		array(
			'controller' => 'FeeSchedule\FeeSchedule',
			'action' => 'index'
		),
	),

	// Site Charge Master
	'clients/sites/charges-master/ajax' => array(
		'/clients/sites/ajax/<id>/charges-master(/<action>(/<subid>))',
		array(
			'controller' => 'Master\Charges\Ajax',
			'action' => 'index'
		),
	),
	'clients/sites/charges-master' => array(
		'/clients/sites/<id>/charges-master(/<action>(/<subid>))',
		array(
			'controller' => 'Master\Charges',
			'action' => 'index'
		),
	),

	'clients/ajax' => array(
		'/clients/ajax/<action>(/<id>)',
		array(
			'controller' => 'Clients\Ajax',
		),
	),
	'clients/sites' => array(
		'/clients/sites/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Clients\Sites',
			'action' => 'index'
		),
	),
	'clients/users' => array(
		'/clients/users/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Clients\Users',
			'action' => 'index'
		),
	),
	'clients/users/ajax/password' => array(
		'/users/ajax/password(/<action>(/<subid>))',
		array(
			'controller' => 'Clients\Users\Ajax\Password',
			'action' => 'index'
		),
	),
	'clients/users/ajax/internal' => array(
		'/users/ajax/internal(/<action>(/<subid>))',
		array(
			'controller' => 'Clients\Internal\Ajax',
			'action' => 'index'
		),
	),
	'clients/users/ajax' => array(
		'/users/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Clients\Users\Ajax',
			'action' => 'index'
		),
	),
	'clients/sites/ajax' => array(
		'/sites/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Clients\Sites\Ajax',
			'action' => 'index'
		),
	),
	'clients' => array(
		'/clients(/<action>(/<id>))',
		array(
			'controller' => 'Clients\Clients',
			'action' => 'index'
		),
	),

	// Overview
	'overview/ajax/dashboard' => array(
		'/overview/ajax/dashboard/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Overview\Ajax\Dashboard',
			'action' => 'index'
		),
	),
	'overview/dashboard' => array(
		'/overview/dashboard/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Overview\Dashboard',
			'action' => 'index'
		),
	),

	// Verification & Pre-Authorization
	'verification/ajax' => array(
		'/verification/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Verification\Ajax',
			'action' => 'index'
		),
	),
	'verification' => array(
		'/verification/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Verification\Verification',
			'action' => 'index'
		),
	),

	// Profile
	'profiles' => array(
		'/profiles/<id>',
		array(
			'controller' => 'Profiles\Profiles',
			'action' => 'index'
		),
	),
	'credentials' => array(
		'/credentials/<id>',
		array(
			'controller' => 'Profiles\Credentials',
			'action' => 'index'
		),
	),
	'profiles/users' => array(
		'/profiles/users/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Profiles\UserProfiles',
			'action' => 'index'
		),
	),
	'profiles/clients' => array(
		'/profiles/clients(/<action>)/<id>',
		array(
			'controller' => 'Profiles\ClientProfiles',
			'action' => 'view'
		),
	),
	'profiles/credentials/ajax' => array(
		'/profiles/credentials/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Profiles\Credentials\Ajax',
			'action' => 'index'
		),
	),

	// Master
	'master/ajax' => array(
		'/master/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Master\Ajax',
			'action' => 'index'
		),
	),
	'master/charges' => array(
		'/master/charges/<id>(/<action>)',
		array(
			'controller' => 'Master\Charges',
			'action' => 'index'
		),
	),
	'master/inventory' => array(
		'/master/inventory/<id>(/<action>)',
		array(
			'controller' => 'Master\Inventory',
			'action' => 'index'
		),
	),

	// Case Registration
	'cases/operative-reports/ajax/note' => array(
		'/cases/operative-reports/ajax/note/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\OperativeReports\Ajax\Note',
			'action' => 'list'
		),
	),
	'cases/registrations/ajax' => array(
		'/cases/registrations/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\Registrations\Ajax',
			'action' => 'index'
		),
	),
	'/cases/registrations' => array(
		'/cases/registrations/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\Registrations\Registrations',
			'action' => 'index'
		),
	),

	'/cases/pre-op' => array(
		'/cases/pre-op/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\PreOp',
			'action' => 'index'
		),
	),
	'/cases/post-op' => array(
		'/cases/post-op/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\PostOp',
			'action' => 'index'
		),
	),
	'/cases/discharge' => array(
		'/cases/discharge/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\Discharge',
			'action' => 'index'
		),
	),

	'/document/printResult' => array(
		'/document/printResult(/<subid>)',
		array(
			'controller' => 'Document\PrintResult',
			'action' => 'printResult'
		),
	),


	// Case Forms
	'cases/forms/ajax' => array(
		'/cases/forms/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\Forms\Ajax',
			'action' => 'index'
		),
	),
	'/cases/forms' => array(
		'/cases/forms/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\Forms\Forms',
			'action' => 'index'
		),
	),

	// Case Forms
	'/cases/operative-reports/ajax' => array(
		'/cases/operative-reports/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\OperativeReports\Ajax',
			'action' => 'index'
		),
	),
	'/cases/operative-reports' => array(
		'/cases/operative-reports/forms/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\OperativeReports\OperativeReports',
			'action' => 'index'
		),
	),

	'organizations/ajax/defaultSitePermissions' => array(
		'/organizations/ajax/defaultSitePermissions',
		array(
			'controller' => 'Clients\Profile\Ajax',
			'action' => 'defaultSitePermissions'
		),
	),

	'organizations/ajax' => array(
		'/organizations/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Clients\Profile\Ajax',
			'action' => 'index'
		),
	),


	// Case Management
	'cases/ajax/save' => array(
		'/cases/ajax/save/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\Ajax\Save',
			'action' => 'list'
		),
	),
	'cases/ajax/discharge' => array(
		'/cases/ajax/discharge/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\Ajax\Discharge',
			'action' => 'list'
		),
	),
	'cases/ajax/coding/claim/' => array(
		'/cases/ajax/coding/claim/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\Ajax\Coding\Claim',
		),
	),
	'cases/ajax/coding' => array(
		'/cases/ajax/coding/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\Ajax\Coding',
		),
	),
	'cases/ajax/intake/pre-operative' => array(
		'/cases/ajax/intake/pre-operative/<id>/<action>(/<subid>)',
		array(
			'controller' => 'Cases\Ajax\Intake\PreOperative'
		),
	),
	'cases/ajax/intake/influenza' => array(
		'/cases/ajax/intake/influenza/<id>/<action>(/<subid>)',
		array(
			'controller' => 'Cases\Ajax\Intake\Influenza'
		),
	),
	'cases/ajax/intake/medications' => array(
		'/cases/ajax/intake/medications/<id>/<action>(/<subid>)',
		array(
			'controller' => 'Cases\Ajax\Intake\Medications'
		),
	),
	'cases/ajax/intake' => array(
		'/cases/ajax/intake/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\Ajax\Intake',
			'action' => 'list'
		),
	),
	'cases/ajax/note' => array(
		'/cases/ajax/note/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\Ajax\Note',
			'action' => 'list'
		),
	),
	'cases/ajax/in-service-note' => array(
		'/cases/ajax/in-service-note/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\Ajax\InServiceNote',
			'action' => 'list'
		),
	),
	'cases/ajax/blocking' => array(
		'/cases/ajax/blocking/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\Ajax\Blocking',
		),
	),
	'cases/ajax' => array(
		'/cases/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\Ajax',
			'action' => 'list'
		),
	),
	'/cases' => array(
		'/cases/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cases\Cases',
			'action' => 'index'
		),
	),

	// Eligible
	'insurances/ajax/eligible' => array(
		'/insurances/ajax/eligible(/<action>(/<subid>))',
		array(
			'controller' => 'Insurances\Eligible\Ajax',
			'action' => 'index'
		),
	),

	// Insurances
	'insurances/ajax' => array(
		'/insurances/ajax(/<action>(/<subid>))',
		array(
			'controller' => 'Insurances\Ajax',
			'action' => 'index'
		),
	),

	// Patients
	'patients/ajax' => array(
		'/patients/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Patients\Ajax',
			'action' => 'index'
		),
	),
	'/patients' => array(
		'/patients/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Patients\Patients',
			'action' => 'index'
		),
	),

	// Patients
	'booking/ajax' => array(
		'/booking/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Booking\Ajax',
			'action' => 'index'
		),
	),
	'/booking' => array(
		'/booking/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Booking\Booking',
			'action' => 'index'
		),
	),

	// Preference Cards
	'cards/ajax/save' => array(
		'/cards/ajax/save(/<action>)',
		array(
			'controller' => 'Cards\Ajax\Save',
		),
	),
	'cards/ajax/prefSave' => array(
		'/cards/ajax/prefSave/<id>(/<action>)',
		array(
			'controller' => 'Cards\Ajax\PrefSave',
		),
	),
	'cards/ajax' => array(
		'/cards/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cards\Ajax',
			'action' => 'list'
		),
	),
	'cards' => array(
		'/cards/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Cards\Cards',
			'action' => 'index'
		),
	),

	// Inventory Multiplier
	'inventory/multiplier/ajax' => array(
		'/inventory-multiplier/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Inventory\Multiplier\Ajax',
			'action' => 'list'
		),
	),
	'inventory/multiplier' => array(
		'/inventory-multiplier/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Inventory\Multiplier\Multiplier',
			'action' => 'index'
		),
	),

	// Inventory Report
	'inventory/report/ajax' => array(
		'/inventory-report/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Inventory\Report\Ajax',
			'action' => 'list'
		),
	),
	'inventory/report' => array(
		'/inventory-report/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Inventory\Report\Report',
			'action' => 'index'
		),
	),

	// Inventory Invoices
	'inventory/invoices/ajax/pdf' => array(
		'/inventory/invoices/ajax/pdf/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Inventory\Invoices\Ajax\PDF',
			'action' => 'index'
		),
	),
	'inventory/invoices/ajax' => array(
		'/inventory/invoices/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Inventory\Invoices\Ajax',
			'action' => 'list'
		),
	),
	'inventory/invoices' => array(
		'/inventory/invoices/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Inventory\Invoices\Invoices',
			'action' => 'index'
		),
	),

	// Inventory Management
	'inventory/ajax' => array(
		'/inventory/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Inventory\Ajax',
			'action' => 'list'
		),
	),
	'inventory/internal' => array(
		'/inventory/internal(/<action>(/<id>))',
		array(
			'controller' => 'Inventory\Internal',
			'action' => 'index'
		),
	),
	'inventory' => array(
		'/inventory/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Inventory\Inventory',
			'action' => 'index'
		),
	),

	// Vendors
	'vendors/ajax' => array(
		'/vendors/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Vendors\Ajax',
			'action' => 'list'
		),
	),
	'vendors/internal' => array(
		'/vendors/internal(/<action>(/<id>))',
		array(
			'controller' => 'Vendors\Internal',
			'action' => 'index'
		),
	),
	'vendors/internal/ajax' => array(
		'/vendors/internal-vendors/ajax(/<action>(/<id>))',
		array(
			'controller' => 'Vendors\Internal\Ajax',
			'action' => 'list'
		),
	),
	'vendors' => array(
		'/vendors/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Vendors\Vendors',
			'action' => 'index'
		),
	),

	// Case Types
	'settings/case-types/ajax' => array(
		'/settings/case-types/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Settings\CaseTypes\Ajax',
			'action' => 'index'
		),
	),
	'settings/case-types' => array(
		'/settings/case-types/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Settings\CaseTypes',
			'action' => 'index'
		),
	),

	// Departments
	'settings/departments/ajax' => array(
		'/settings/departments/ajax(/<action>(/<id>))',
		array(
			'controller' => 'Settings\Departments\Ajax',
			'action' => 'index'
		),
	),

	// Practice Groups
	'settings/practice-groups/ajax' => array(
		'/settings/practice-groups/ajax(/<action>(/<id>))',
		array(
			'controller' => 'Settings\PracticeGroups\Ajax',
			'action' => 'index'
		),
	),

	// Purchase orders
	'orders/internal' => array(
		'/orders/internal(/<action>(/<id>))',
		array(
			'controller' => 'Orders\Internal',
			'action' => 'index'
		),
	),
	'orders/ajax/internal' => array(
		'/orders/ajax/internal(/<action>(/<id>))',
		array(
			'controller' => 'Orders\Ajax\Internal',
			'action' => 'index'
		),
	),
	'orders/ajax/received' => array(
		'/orders/ajax/received/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Orders\Ajax\Received',
			'action' => 'index'
		),
	),
	'orders/ajax/outgoing' => array(
		'/orders/ajax/outgoing/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Orders\Ajax\Outgoing',
			'action' => 'index'
		),
	),
	'orders/outgoing' => array(
		'/orders/outgoing/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Orders\Outgoing',
			'action' => 'index'
		),
	),
	'orders' => array(
		'/orders/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Orders\Orders',
			'action' => 'index'
		),
	),

	// Operative-reports
	'operative-reports/ajax/save' => array(
		'/operative-reports/ajax/save/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'OperativeReports\Ajax\Save',
			'action' => 'index'
		),
	),
	'operative-reports/ajax' => array(
		'/operative-reports/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'OperativeReports\Ajax',
			'action' => 'index'
		),
	),
	'operative-reports/my' => array(
		'/operative-reports/my/<id>(/<action>(/<subid>(/<userid>)))',
		array(
			'controller' => 'OperativeReports\My',
			'action' => 'index'
		),
	),
	'operative-reports' => array(
		'/operative-reports/<id>(/<action>(/<subid>(/<userid>)))',
		array(
			'controller' => 'OperativeReports\OperativeReports',
			'action' => 'index'
		),
	),

	// Billings
	'billings/ajax/note' => array(
		'/billings/ajax/note/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\Ajax\Note',
			'action' => 'list'
		),
	),
	'billings/reports/ajax' => array(
		'/billings/reports/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\Reports\Ajax',
			'action' => 'index'
		),
	),
	'billings/reports' => array(
		'/billings/reports/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\Reports\Reports',
			'action' => 'index'
		),
	),
	'billings/collections/ajax' => array(
		'/billings/collections/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\Collections\Ajax',
			'action' => 'index'
		),
	),
	'billings/collections' => array(
		'/billings/collections/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\Collections\Collections',
			'action' => 'index'
		),
	),
	'billings/eob-management/ajax' => array(
		'/billings/eob-management/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\EOB\Ajax',
			'action' => 'index'
		),
	),
	'billings/eob-management' => array(
		'/billings/eob-management/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\EOB\EOB',
			'action' => 'index'
		),
	),

	'billings/claims-management/ajax' => array(
		'/billings/claims-management/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\ClaimsManagement\Ajax',
			'action' => 'index'
		)
	),

	'billings/claims-management' => array(
		'/billings/claims-management/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\ClaimsManagement\Index',
			'action' => 'index'
		)
	),

	'billings/batch-eligibility/ajax' => array(
		'/billings/batch-eligibility/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\BatchEligibility\Ajax',
			'action' => 'index'
		)
	),

	'billings/batch-eligibility' => array(
		'/billings/batch-eligibility/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\BatchEligibility\Eligibility',
			'action' => 'index'
		)
	),

	'billings/claims-processing/ajax' => array(
		'/billings/claims-processing/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\ClaimsProcessing\Ajax',
			'action' => 'index'
		)
	),

	'billings/claims-processing' => array(
		'/billings/claims-processing/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\ClaimsProcessing\Index',
			'action' => 'index'
		)
	),

	'billings/ledger/ajax/interests' => array(
		'/billings/ledger/ajax/interests/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\Ledger\Ajax\Interests',
			'action' => 'index'
		)
	),


	'billings/ledger/ajax' => array(
		'/billings/ledger/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\Ledger\Ajax',
			'action' => 'index'
		)
	),

	'billings/ledger' => array(
		'/billings/ledger/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\Ledger\Index',
			'action' => 'index'
		)
	),

	'billings/patient-statement/ajax' => array(
		'/billings/patient-statement/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\PatientStatement\Ajax',
			'action' => 'index'
		)
	),

	'billings/patient-statement' => array(
		'/billings/patient-statement/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\PatientStatement\Index',
			'action' => 'index'
		)
	),

	'billings/itemized-bill/ajax' => array(
		'/billings/itemized-bill/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\ItemizedBill\Ajax',
			'action' => 'index'
		)
	),

	'billings/itemized-bill' => array(
		'/billings/itemized-bill/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\ItemizedBill\Index',
			'action' => 'index'
		)
	),

	'billings/ledger-payment-activity/ajax' => array(
		'/billings/ledger-payment-activity/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\LedgerPaymentActivity\Ajax',
			'action' => 'index'
		)
	),

	'billings/ledger-payment-activity' => array(
		'/billings/ledger-payment-activity/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\LedgerPaymentActivity\Index',
			'action' => 'index'
		)
	),

	'billings/ajax' => array(
		'/billings/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\Ajax',
			'action' => 'index'
		),
	),
	'billings' => array(
		'/billings/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Billings\Billings',
			'action' => 'index'
		),
	),

	// Analitics
	'analytics/internal/ajax' => array(
		'/analytics/internal/ajax(/<action>(/<id>))',
		array(
			'controller' => 'Analytics\Internal\Ajax',
			'action' => 'index'
		),
	),
	'analytics/internal' => array(
		'/analytics/internal(/<action>(/<id>))',
		array(
			'controller' => 'Analytics\Internal',
			'action' => 'index'
		),
	),
	'analytics/reports/ajax' => array(
		'/analytics/reports/ajax(/<action>(/<subid>))',
		array(
			'controller' => 'Analytics\Reports\Ajax',
			'action' => 'index'
		),
	),
	'analytics/reports' => array(
		'/analytics/reports/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Analytics\Reports\Reports',
			'action' => 'index'
		),
	),
	'analytics/credentials/ajax' => array(
		'/analytics/credentials/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Analytics\Credentials\Ajax',
			'action' => 'index'
		),
	),
	'analytics/credentials/medical' => array(
		'/analytics/credentials/medical/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Analytics\Credentials\Credentials',
			'action' => 'medical'
		),
	),
	'analytics/credentials/non-surgical' => array(
		'/analytics/credentials/non-surgical/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Analytics\Credentials\Credentials',
			'action' => 'nonSurgical'
		),
	),
	'analytics/sms-log/ajax' => array(
		'/analytics/sms-log/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Analytics\SmsLog\Ajax',
			'action' => 'index'
		),
	),
	'analytics/sms-log' => array(
		'/analytics/sms-log/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Analytics\SmsLog\SmsLog',
			'action' => 'index'
		),
	),
	'analytics/ajax' => array(
		'/analytics/ajax(/<action>(/<subid>))',
		array(
			'controller' => 'Analytics\Ajax',
			'action' => 'index'
		),
	),
	'analytics' => array(
		'/analytics/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Analytics\Analytics',
			'action' => 'index'
		),
	),


	'patient-users/internal/ajax' => array(
		'/patient-users/internal/ajax(/<action>(/<id>))',
		array(
			'controller' => 'Patients\Portal\UserDatabase\Ajax',
			'action' => 'index'
		),
	),

	'patient-users/internal' => array(
		'/patient-users/internal(/<action>(/<id>))',
		array(
			'controller' => 'Patients\Portal\UserDatabase\Index',
			'action' => 'index'
		),
	),

	'image/ajax/upload' => array(
		'/image/ajax/upload',
		array(
			'controller' => 'File\Image\Ajax',
			'action' => 'upload'
		),
	),

	'image/ajax/uploadContent' => array(
		'/image/ajax/uploadContent',
		array(
			'controller' => 'File\Image\Ajax',
			'action' => 'uploadContent'
		),
	),

	'file/ajax/upload' => array(
		'/file/ajax/upload',
		array(
			'controller' => 'File\Ajax',
			'action' => 'upload'
		),
	),

	'file/genpdf' => array(
		'/file/genpdf',
		array(
			'controller' => 'File\ProtectedFile',
			'action' => 'genpdf'
		),
	),

	'file/view' => array(
		'/file/view',
		array(
			'controller' => 'File\ProtectedFile',
			'action' => 'view'
		),
	),

	'settings/fields/ajax' => array(
		'/settings/fields/ajax(/<action>(/<id>))',
		array(
			'controller' => 'Settings\Fields\Ajax',
			'action' => 'index'
		),
	),

	'settings/fields' => array(
		'/settings/fields(/<action>(/<id>))',
		array(
			'controller' => 'Settings\Fields',
			'action' => 'index'
		),
	),

	'settings/databases/hcpc/ajax' => array(
		'/settings/databases/hcpc/ajax(/<action>(/<id>))',
		array(
			'controller' => 'Settings\Databases\HCPC\Ajax',
			'action' => 'index'
		),
	),
	'settings/databases/hcpc' => array(
		'/settings/databases/hcpc(/<action>(/<id>))',
		array(
			'controller' => 'Settings\Databases\HCPC\Index',
			'action' => 'index'
		),
	),

	'settings/databases/cpt/ajax' => array(
		'/settings/databases/cpt/ajax(/<action>(/<id>))',
		array(
			'controller' => 'Settings\Databases\CPT\Ajax',
			'action' => 'index'
		),
	),
	'settings/databases/cpt' => array(
		'/settings/databases/cpt(/<action>(/<id>))',
		array(
			'controller' => 'Settings\Databases\CPT\CPT',
			'action' => 'index'
		),
	),

	'settings/databases/icd/ajax' => array(
		'/settings/databases/icd/ajax(/<action>(/<id>))',
		array(
			'controller' => 'Settings\Databases\ICD\Ajax',
			'action' => 'index'
		),
	),
	'settings/databases/icd' => array(
		'/settings/databases/icd(/<action>(/<id>))',
		array(
			'controller' => 'Settings\Databases\ICD\ICD',
			'action' => 'index'
		),
	),

	'settings/databases/insurance-payors/ajax' => array(
		'/settings/databases/insurance-payors/ajax(/<action>(/<id>))',
		array(
			'controller' => 'Settings\Databases\InsurancePayors\Ajax',
			'action' => 'index'
		),
	),
	'settings/databases/insurance-payors' => array(
		'/settings/databases/insurance-payors(/<action>)',
		array(
			'controller' => 'Settings\Databases\InsurancePayors\Index',
			'action' => 'index'
		),
	),

	'settings/databases/pref-card-stages/ajax' => array(
		'/settings/databases/pref-card-stages/ajax(/<action>(/<id>))',
		array(
			'controller' => 'Settings\Databases\PrefCardStages\Ajax',
			'action' => 'index'
		),
	),
	'settings/databases/pref-card-stages' => array(
		'/settings/databases/pref-card-stages',
		array(
			'controller' => 'Settings\Databases\PrefCardStages\Index',
			'action' => 'index'
		),
	),

	'settings/databases/uom/ajax' => array(
		'/settings/databases/uom/ajax(/<action>(/<id>))',
		array(
			'controller' => 'Settings\Databases\UOM\Ajax',
			'action' => 'index'
		),
	),
	'settings/databases/uom' => array(
		'/settings/databases/uom',
		array(
			'controller' => 'Settings\Databases\UOM\Index',
			'action' => 'index'
		),
	),

	'settings/logs/navicure/ajax' => array(
		'/settings/logs/navicure/ajax(/<action>(/<id>))',
		array(
			'controller' => 'Settings\Logs\Navicure\Ajax',
			'action' => 'index'
		),
	),
	'settings/logs/navicure' => array(
		'/settings/logs/navicure(/<action>(/<id>))',
		array(
			'controller' => 'Settings\Logs\Navicure\Navicure',
			'action' => 'index'
		),
	),

	'settings/patient-portal/ajax' => array(
		'/settings/patient-portal/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Settings\PatientPortal\Ajax',
			'action' => 'index'
		),
	),

	'settings/patient-portal' => array(
		'/settings/patient-portal/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Settings\PatientPortal',
			'action' => 'index'
		),
	),

	'settings/forms/charts/ajax/custom' => array(
		'/settings/forms/charts/ajax/custom(/<id>(/<action>(/<subid>)))',
		array(
			'controller' => 'Settings\Forms\Charts\Custom',
			'action' => 'index'
		),
	),
	'settings/forms/charts/ajax/uploaded' => array(
		'/settings/forms/charts/ajax/uploaded(/<id>(/<action>(/<subid>)))',
		array(
			'controller' => 'Settings\Forms\Charts\Uploaded',
			'action' => 'index'
		),
	),
	'settings/forms/charts/ajax/pdf' => array(
		'/settings/forms/charts/ajax/pdf/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Settings\Forms\Charts\Ajax\PDF',
			'action' => 'index'
		),
	),
	'settings/forms/charts/ajax' => array(
		'/settings/forms/charts/ajax(/<id>(/<action>(/<subid>)))',
		array(
			'controller' => 'Settings\Forms\Charts\Ajax',
			'action' => 'index'
		),
	),
	'settings/forms/charts' => array(
		'/settings/forms/charts(/<id>(/<action>(/<subid>)))',
		array(
			'controller' => 'Settings\Forms\Charts\Index',
			'action' => 'index'
		),
	),


	'settings/forms/chart-groups/ajax' => array(
		'/settings/forms/chart-groups/ajax(/<id>(/<action>(/<subid>)))',
		array(
			'controller' => 'Settings\Forms\ChartGroups\Ajax',
			'action' => 'index'
		),
	),
	'settings/forms/chart-groups' => array(
		'/settings/forms/chart-groups(/<id>(/<action>(/<subid>)))',
		array(
			'controller' => 'Settings\Forms\ChartGroups\Index',
			'action' => 'index'
		),
	),

	// Alerts
	'settings/alerts/ajax' => array(
		'/settings/alerts/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Settings\Alerts\Ajax',
			'action' => 'index'
		),
	),
	'settings/alerts' => array(
		'/settings/alerts/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Settings\Alerts',
			'action' => 'index'
		),
	),

	'settings' => array(
		'/settings(/<action>)',
		array(
			'controller' => 'Settings\Settings',
		),
	),

	// SMS Template
	'sms-template/ajax' => array(
		'/sms-template/ajax/<id>(/<action>)',
		array(
			'controller' => 'Settings\SmsTemplate\Ajax',
			'action' => 'index'
		),
	),
	'sms-template' => array(
		'/sms-template/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Settings\SmsTemplate',
			'action' => 'index'
		),
	),

	// Chat
	'chat/ajax' => array(
		'/chat/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Chat\Ajax',
			'action' => 'index'
		),
	),
	'chat' => array(
		'/chat/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Chat\Chat',
			'action' => 'index'
		),
	),

	'messaging/ajax' => array(
		'/messaging/ajax/<id>(/<action>(/<subid>))',
		array(
			'controller' => 'Messaging\Ajax',
			'action' => 'index'
		),
	),


	'efax/ajax' => array(
		'/efax/ajax(/<action>(/<subid>))',
		array(
			'controller' => 'Efax\Ajax',
			'action' => 'index'
		),
	),
	'efax' => array(
		'/efax(/<action>(/<subid>))',
		array(
			'controller' => 'Efax\Efax',
			'action' => 'index'
		),
	),

	'reminder/ajax' => array(
		'/reminder/ajax(/<action>(/<subid>))',
		array(
			'controller' => 'Reminder\Ajax',
			'action' => 'index'
		),
	),

	// Static
	'page' => array(
		'/page(/<action>)',
		array(
			'controller' => 'Page',
		),
	),

	'homepage' => array(
		'/(<action>)',
		array(
			'controller' => 'PublicPage',
			'action' => 'index'
		),
	),
	'default' => array(
		'(/<controller>(/<action>(/<id>)))',
		array(
			'action' => 'index'
		),
	),

);
