<?php

use Opake\Model\Profession;
use Opake\Model\Role;

return [
	'role' => [
		Role::FullAdmin => [
			'cases' => [
				'create' => true,
				'view' => true,
				'index' => true,
				'edit' => true,
				'edit_by_calendar' => true,
				'edit_assigned_users' => true,
				'delete' => true,
				'import_from_excel' => true
			],
			'cancellation' => [
				'index' => true,
				'reschedule' => true
			],
			'case_blocks' => [
				'create' => true,
				'edit' => true,
				'view' => true
			],
			'case_types' => [
				'create' => true,
			],
			'card' => [
				'index' => true,
				'create' => true,
				'view' => true,
				'edit' => true,
				'assign_to_case' => true
			],
			'inventory' => [
				'index' => true,
				'create' => true,
				'edit' => true,
				'edit_quantities' => false,
				'view' => true,
				'move' => true,
				'order' => true,
				'receive' => true, // recieve inventory
				'delete' => true,
			],
			'chat' => [
				'messaging' => true,
				'view_history' => false,
			],
			'analytics' => [// reports
				'view' => true,
				'view_reports' => true,
				'view_credentials' => true,
				'view_sms_log' => true,
				'view_billing' => true
			],
			'user' => [
				'view' => true,
				'edit' => true,
				'create' => false, // Manage users, с заделом на будущее,
				'edit_permissions' => true,
				'edit_not_basic' => true,
				'edit_practice_groups' => true,
				'send_password_email' => true,
				'delete' => false,
			],
			'patients' => [
				'create' => true,
				'view' => true,
				'edit' => true,
				'index' => true,
				'import_from_excel' => true
			],
			'booking' => [
				'index' => true,
				'create' => true,
				'view' => true,
				'edit' => true,
				'delete' => true,
				'edit_assigned_users' => true
			],
			'operative_reports' => [
				'index' => true,
				'index_surgeons' => true,
				'view' => true,
				'edit' => false,
				'reopen' => false,
				'create' => false,
				'print' => true,
				'archive' => true,
				'dragon_dictation' => true,
				'sign' => false,
			],
			'surgeon_templates' => [
				'index' => true,
				'view' => true,
				'edit' => true,
				'create' => true,
			],
			'site_template' => [
				'edit' => true,
			],
			'schedule' => [
				'view_settings' => true,
				'index' => true
			],
			'billing' => [
				'view' => true,
				'index' => true,
				'reports' => true,
				'view_forms' => true,
				'eligibility' => true,
				'collections' => true,
				'eob' => true,
				'notes' => true
			],
			'organization' => [
				'create' => false,
				'view' => true,
				'edit' => false
			],
			'sites' => [
				'view' => true,
				'edit' => false,
				'create' => false,
			],
			'vendors' => [
				'view' => true,
				'create' => true,
				'edit' => true
			],
			'databases' => [
				'view' => true
			],
			'insurance' => [
				'view' => true
			],
			'forms' => [
				'view' => true
			],
			'registration' => [
				'index' => true,
				'view' => true,
				'edit' => true
			],
			'case_management' => [
				'view' => true,
				'view_appointment_buttons' => true
			],
			'case_management_intake' => [
				'view' => true
			],
			'case_management_clinical' => [
				'view' => true,
				'view_hp' => true,
				'view_pre_op' => true,
				'view_op' => true,
				'view_post_op' => true,
				'view_op_report' => true,
				'view_discharge' => true,
				'view_inventory' => true,
				'pre_populate_pref_card' => true
			],
			'case_management_item_log' => [
				'view' => true
			],
			'case_management_time_log' => [
				'view' => true
			],
			'case_management_audit' => [
				'view' => true
			],
			'patient-portal' => [
				'settings' => false,
				'send_login_email' => true
			],
			'verifications' => [
				'index' => true,
				'view' => true
			],
			'sms-template' => [
				'index' => true
			],
			'alerts' => [
				'settings' => true
			],
			'booking_sheet_template' => [
				'view' => true,
				'edit' => true,
				'create' => true,
				'delete' => true
			],
			'efax' => [
				'view' => true
			],
			'dashboard' => [
				'index' => true,
			],
			'financial_documents' => [
				'index' => true,
			],
		],
		Role::FullClinical => [
			'cases' => [
				'create' => true,
				'view' => true,
				'edit' => true,
				'index' => true,
				'edit_by_calendar' => true,
				'edit_assigned_users' => true,
				'delete' => true,
				'import_from_excel' => false
			],
			'cancellation' => [
				'index' => true,
				'reschedule' => true
			],
			'case_blocks' => [
				'create' => true,
				'edit' => true
			],
			'case_types' => [
				'create' => false,
				'edit' => true,
				'view' => true
			],
			'card' => [
				'index' => true,
				'create' => true,
				'view' => true,
				'edit' => true,
				'assign_to_case' => true
			],
			'inventory' => [
				'index' => true,
				'view' => true,
				'create' => false,
				'edit' => false,
				'edit_quantities' => true,
				'move' => true,
				'order' => true,
				'receive' => true,
				'delete' => false,
			],
			'chat' => [
				'messaging' => true,
				'view_history' => false,
			],
			'analytics' => [
				'view' => false,
				'view_reports' => true,
				'view_credentials' => false,
				'view_sms_log' => false
			],
			'user' => [
				'view' => 'self',
				'edit' => 'self',
				'create' => false,
				'edit_permissions' => false,
				'edit_not_basic' => false,
				'edit_practice_groups' => true,
				'send_password_email' => false,
				'delete' => false,
			],
			'patients' => [
				'create' => true,
				'view' => true,
				'edit' => true,
				'index' => true,
				'import_from_excel' => false
			],
			'booking' => [
				'index' => true,
				'create' => true,
				'view' => true,
				'edit' => true,
				'delete' => true,
				'edit_assigned_users' => true
			],
			'operative_reports' => [
				'index' => true,
				'index_surgeons' => false,
				'view' => true,
				'edit' => 'self',
				'reopen' => false,
				'create' => true,
				'print' => true,
				'archive' => true,
				'dragon_dictation' => true,
				'sign' => false,
			],
			'surgeon_templates' => [
				'index' => true,
				'view' => true,
				'edit' => 'self',
				'create' => true,
			],
			'site_template' => [
				'edit' => false,
			],
			'schedule' => [
				'view_settings' => false,
				'index' => true
			],
			'billing' => [
				'view' => false,
				'index' => false,
				'reports' => false,
				'view_forms' => false,
				'eligibility' => false,
				'collections' => false,
				'eob' => false,
			],
			'organization' => [
				'create' => false,
				'view' => false,
				'edit' => false
			],
			'sites' => [
				'view' => false,
				'edit' => false,
				'create' => false,
			],
			'vendors' => [
				'view' => false,
				'create' => false,
				'edit' => false
			],
			'databases' => [
				'view' => false
			],
			'insurance' => [
				'view' => false
			],
			'forms' => [
				'view' => false
			],
			'registration' => [
				'index' => true,
				'view' => true,
				'edit' => true
			],
			'case_management' => [
				'view' => true,
				'view_appointment_buttons' => true
			],
			'case_management_intake' => [
				'view' => true
			],
			'case_management_clinical' => [
				'view' => true,
				'view_hp' => true,
				'view_pre_op' => true,
				'view_op' => true,
				'view_post_op' => true,
				'view_op_report' => true,
				'view_discharge' => true,
				'view_inventory' => true,
				'pre_populate_pref_card' => true
			],
			'case_management_item_log' => [
				'view' => true
			],
			'case_management_time_log' => [
				'view' => true
			],
			'case_management_audit' => [
				'view' => false
			],
			'patient-portal' => [
				'settings' => false,
				'send_login_email' => true
			],
			'verifications' => [
				'index' => false,
				'view' => false
			],
			'sms-template' => [
				'index' => true
			],
			'alerts' => [
				'settings' => false
			],
		    'booking_sheet_template' => [
			    'view' => false,
		        'edit' => false,
		        'create' => false,
		        'delete' => false
		    ],
			'efax' => [
				'view' => true
			],
			'dashboard' => [
				'index' => true,
			]

		],
		Role::Doctor => [
			'cases' => [
				'create' => false,
				'view' => 'self',
				'index' => true,
				'edit' => false,
				'edit_by_calendar' => false,
				'edit_assigned_users' => false,
				'delete' => false,
				'import_from_excel' => false
			],
			'cancellation' => [
				'index' => false,
				'reschedule' => false
			],
			'case_blocks' => [
				'create' => false,
				'edit' => false,
				'view' => 'self'
			],
			'case_types' => [
				'create' => false,
			],
			'card' => [
				'index' => true,
				'create' => true,
				'view' => 'self',
				'edit' => 'self',
				'assign_to_case' => true
			],
			'inventory' => [
				'index' => false,
				'create' => false,
				'edit' => false,
				'edit_quantities' => false,
				'view' => true,
				'move' => false,
				'order' => false,
				'receive' => false,
				'delete' => false,
			],
			'chat' => [
				'messaging' => true,
				'view_history' => false,
			],
			'analytics' => [
				'view' => false,
				'view_reports' => false,
				'view_credentials' => false,
				'view_sms_log' => false
			],
			'user' => [
				'view' => 'self',
				'edit' => 'self',
				'create' => false,
				'edit_permissions' => false,
				'edit_not_basic' => false,
				'edit_practice_groups' => true,
				'send_password_email' => false,
				'delete' => false,
			],
			'patients' => [
				'create' => false,
				'view' => 'self',
				'edit' => 'self',
				'index' => true,
				'import_from_excel' => false
			],
			'booking' => [
				'index' => false,
				'create' => false,
				'view' => false,
				'edit' => false,
				'delete' => false,
				'edit_assigned_users' => false
			],
			'operative_reports' => [
				'index' => true,
				'index_surgeons' => false,
				'view' => 'self',
				'edit' => 'self',
				'reopen' => 'self',
				'create' => 'self',
				'dragon_dictation' => true,
				'sign' => 'self',
			],
			'surgeon_templates' => [
				'index' => true,
				'view' => 'self',
				'edit' => 'self',
				'create' => 'self',
			],
			'site_template' => [
				'edit' => false,
			],
			'schedule' => [
				'view_settings' => false,
				'index' => true
			],
			'billing' => [
				'view' => false,
				'index' => true,
				'reports' => false,
				'view_forms' => false,
				'eligibility' => false,
				'collections' => true,
				'eob' => false,
				'notes' => true,
			],
			'organization' => [
				'create' => false,
				'view' => false,
				'edit' => false
			],
			'sites' => [
				'view' => false,
				'edit' => false,
				'create' => false,
			],
			'vendors' => [
				'view' => false,
				'create' => false,
				'edit' => false
			],
			'databases' => [
				'view' => false
			],
			'insurance' => [
				'view' => false
			],
			'forms' => [
				'view' => false
			],
			'registration' => [
				'index' => false,
				'view' => true,
				'edit' => false
			],
			'case_management' => [
				'view' => true,
				'view_appointment_buttons' => false
			],
			'case_management_intake' => [
				'view' => true
			],
			'case_management_clinical' => [
				'view' => true,
				'view_hp' => true,
				'view_pre_op' => true,
				'view_op' => true,
				'view_post_op' => true,
				'view_op_report' => true,
				'view_discharge' => true,
				'view_inventory' => true,
				'pre_populate_pref_card' => true
			],
			'case_management_item_log' => [
				'view' => true
			],
			'case_management_time_log' => [
				'view' => true
			],
			'case_management_audit' => [
				'view' => false
			],
			'patient-portal' => [
				'settings' => false,
				'send_login_email' => false
			],
			'verifications' => [
				'index' => false,
				'view' => false
			],
			'sms-template' => [
				'index' => false
			],
			'alerts' => [
				'settings' => false
			],
			'booking_sheet_template' => [
				'view' => false,
				'edit' => false,
				'create' => false,
				'delete' => false
			],
			'efax' => [
				'view' => false
			],
			'dashboard' => [
				'index' => true,
			]
		],
		Role::SatelliteOffice => [
			'cases' => [
				'create' => false,
				'view' => 'self',
				'index' => true,
				'edit' => 'self',
				'edit_by_calendar' => false,
				'edit_assigned_users' => false,
				'delete' => false,
				'import_from_excel' => false
			],
			'cancellation' => [
				'index' => false,
				'reschedule' => false
			],
			'case_blocks' => [
				'create' => false,
				'edit' => false,
				'view' => false
			],
			'case_types' => [
				'create' => false,
			],
			'card' => [
				'index' => true,
				'create' => true,
				'view' => 'self',
				'edit' => 'self',
				'assign_to_case' => true
			],
			'inventory' => [
				'index' => false,
				'create' => false,
				'edit' => false,
				'edit_quantities' => false,
				'view' => false,
				'move' => false,
				'order' => false,
				'receive' => false,
				'delete' => false,
			],
			'chat' => [
				'messaging' => true,
				'view_history' => false,
			],
			'analytics' => [
				'view' => false,
				'view_reports' => false,
				'view_credentials' => true,
				'view_sms_log' => false
			],
			'user' => [
				'view' => 'self',
				'edit' => 'self',
				'create' => false,
				'edit_permissions' => false,
				'edit_not_basic' => false,
				'edit_practice_groups' => true,
				'send_password_email' => false,
				'delete' => false,
			],
			'patients' => [
				'create' => true,
				'view' => 'self',
				'edit' => 'self',
				'index' => true,
				'import_from_excel' => false
			],
			'booking' => [
				'index' => true,
				'create' => true,
				'view' => 'self',
				'edit' => 'self',
				'delete' => true,
				'edit_assigned_users' => true
			],
			'operative_reports' => [
				'index' => true,
				'index_surgeons' => true,
				'view' => true,
				'edit' => true,
				'reopen' => true,
				'create' => true,
				'dragon_dictation' => true,
				'sign' => false,
			],
			'surgeon_templates' => [
				'index' => true,
				'view' => true,
				'edit' => true,
				'create' => true,
			],
			'site_template' => [
				'edit' => false,
			],
			'schedule' => [
				'view_settings' => false,
				'index' => true
			],
			'billing' => [
				'view' => false,
				'index' => false,
				'reports' => false,
				'view_forms' => false,
				'eligibility' => false,
				'collections' => false,
				'eob' => false,
			],
			'organization' => [
				'create' => false,
				'view' => false,
				'edit' => false
			],
			'sites' => [
				'view' => false,
				'edit' => false,
				'create' => false,
			],
			'vendors' => [
				'view' => false,
				'create' => false,
				'edit' => false
			],
			'databases' => [
				'view' => false
			],
			'insurance' => [
				'view' => false
			],
			'forms' => [
				'view' => false
			],
			'registration' => [
				'index' => false,
				'view' => 'self',
				'edit' => false
			],
			'case_management' => [
				'view' => true,
				'view_appointment_buttons' => false
			],
			'case_management_intake' => [
				'view' => true
			],
			'case_management_clinical' => [
				'view' => true,
				'view_hp' => true,
				'view_pre_op' => true,
				'view_op' => true,
				'view_post_op' => true,
				'view_op_report' => true,
				'view_discharge' => true,
				'view_inventory' => true,
				'pre_populate_pref_card' => true
			],
			'case_management_item_log' => [
				'view' => true
			],
			'case_management_time_log' => [
				'view' => true
			],
			'case_management_audit' => [
				'view' => false
			],
			'patient-portal' => [
				'settings' => false,
				'send_login_email' => false
			],
			'verifications' => [
				'index' => false,
				'view' => false
			],
			'sms-template' => [
				'index' => false
			],
			'alerts' => [
				'settings' => false
			],
			'booking_sheet_template' => [
				'view' => false,
				'edit' => false,
				'create' => false,
				'delete' => false
			],
			'efax' => [
				'view' => false
			],
			'dashboard' => [
				'index' => true,
			]
		],
		Role::Dictation => [
			'cases' => [
				'index' => false,
			],
			'user' => [
				'view' => 'self',
				'edit' => false,
				'create' => false,
				'edit_permissions' => false,
				'edit_not_basic' => false,
				'edit_practice_groups' => false,
				'send_password_email' => false,
				'delete' => false,
			],
			'patients' => [
				'index' => false,
			],
			'operative_reports' => [
				'index' => true,
				'index_surgeons' => true,
				'view' => true,
				'edit' => true,
				'reopen' => false,
				'create' => false,
				'print' => true,
				'archive' => true,
				'dragon_dictation' => true,
				'sign' => false,
			],
			'schedule' => [
				'index' => false
			],
			'dashboard' => [
				'index' => false,
			]
		],
		Role::Biller => [
			'billing' => [
				'view' => true,
				'index' => true,
				'reports' => true,
				'view_forms' => true,
				'eligibility' => true,
				'collections' => true,
				'eob' => true,
				'send_claim' => true,
				'post_payment' => true,
				'generate_patient_statement' => true,
				'edit_ledger_transaction' => true,
				'notes' => true
			],
			'financial_documents' => [
				'index' => true,
			],
			'cases' => [
				'create' => false,
				'view' => true,
				'index' => true,
				'edit' => true,
				'edit_by_calendar' => true,
				'edit_assigned_users' => true,
				'delete' => false,
				'import_from_excel' => true
			],
			'cancellation' => [
				'index' => true,
				'reschedule' => false,
			],
			'case_blocks' => [
				'create' => true,
				'edit' => true,
				'view' => true
			],
			'case_types' => [
				'create' => true,
			],
			'card' => [
				'index' => true,
				'create' => true,
				'view' => true,
				'edit' => true,
				'assign_to_case' => true
			],
			'inventory' => [
				'index' => true,
				'create' => true,
				'edit' => true,
				'edit_quantities' => false,
				'view' => true,
				'move' => true,
				'order' => true,
				'receive' => true, // recieve inventory
				'delete' => true,
			],
			'chat' => [
				'messaging' => true,
				'view_history' => false,
			],
			'analytics' => [// reports
				'view' => true,
				'view_reports' => true,
				'view_credentials' => true,
				'view_sms_log' => true,
				'view_billing' => true
			],
			'user' => [
				'view' => true,
				'edit' => true,
				'create' => false, // Manage users, с заделом на будущее,
				'edit_permissions' => true,
				'edit_not_basic' => true,
				'edit_practice_groups' => true,
				'send_password_email' => true,
				'delete' => false,
			],
			'patients' => [
				'create' => false,
				'view' => true,
				'edit' => true,
				'index' => true,
				'import_from_excel' => true
			],
			'booking' => [
				'index' => true,
				'create' => false,
				'view' => true,
				'edit' => true,
				'delete' => true,
				'edit_assigned_users' => true
			],
			'operative_reports' => [
				'index' => true,
				'index_surgeons' => true,
				'view' => true,
				'edit' => false,
				'reopen' => false,
				'create' => false,
				'print' => true,
				'archive' => true,
				'dragon_dictation' => true,
				'sign' => false,
			],
			'surgeon_templates' => [
				'index' => true,
				'view' => true,
				'edit' => true,
				'create' => true,
			],
			'site_template' => [
				'edit' => true,
			],
			'schedule' => [
				'view_settings' => true,
				'index' => true
			],
			'organization' => [
				'create' => false,
				'view' => true,
				'edit' => false
			],
			'sites' => [
				'view' => true,
				'edit' => false,
				'create' => false,
			],
			'vendors' => [
				'view' => true,
				'create' => true,
				'edit' => true
			],
			'databases' => [
				'view' => true
			],
			'insurance' => [
				'view' => true
			],
			'forms' => [
				'view' => true
			],
			'registration' => [
				'index' => false,
				'view' => true,
				'edit' => false
			],
			'case_management' => [
				'view' => true,
				'view_appointment_buttons' => false
			],
			'case_management_intake' => [
				'view' => true
			],
			'case_management_clinical' => [
				'view' => true,
				'view_hp' => true,
				'view_pre_op' => true,
				'view_op' => true,
				'view_post_op' => true,
				'view_op_report' => true,
				'view_discharge' => true,
				'view_inventory' => true,
				'pre_populate_pref_card' => true
			],
			'case_management_item_log' => [
				'view' => true
			],
			'case_management_time_log' => [
				'view' => true
			],
			'case_management_audit' => [
				'view' => true
			],
			'patient-portal' => [
				'settings' => false,
				'send_login_email' => true
			],
			'verifications' => [
				'index' => true,
				'view' => true
			],
			'sms-template' => [
				'index' => true
			],
			'alerts' => [
				'settings' => true
			],
			'booking_sheet_template' => [
				'view' => true,
				'edit' => true,
				'create' => true,
				'delete' => true
			],
			'efax' => [
				'view' => true
			],
			'dashboard' => [
				'index' => true,
			]
		],
		Role::Scheduler => [
			'cases' => [
				'create' => true,
				'view' => true,
				'index' => true,
				'edit' => true,
				'edit_by_calendar' => true,
				'edit_assigned_users' => true,
				'delete' => true,
				'import_from_excel' => true
			],
			'cancellation' => [
				'index' => true,
				'reschedule' => true
			],
			'case_blocks' => [
				'create' => true,
				'edit' => true,
				'view' => true
			],
			'case_types' => [
				'create' => true,
			],
			'card' => [
				'index' => true,
				'create' => true,
				'view' => true,
				'edit' => true,
				'assign_to_case' => true
			],
			'inventory' => [
				'index' => true,
				'create' => true,
				'edit' => true,
				'edit_quantities' => false,
				'view' => true,
				'move' => true,
				'order' => true,
				'receive' => true, // recieve inventory
				'delete' => true,
			],
			'chat' => [
				'messaging' => true,
				'view_history' => false,
			],
			'analytics' => [// reports
				'view' => false,
				'view_reports' => true,
				'view_credentials' => false,
				'view_sms_log' => false,
				'view_billing' => true
			],
			'user' => [
				'view' => true,
				'edit' => false,
				'create' => false, // Manage users, с заделом на будущее,
				'edit_permissions' => false,
				'edit_not_basic' => true,
				'edit_practice_groups' => true,
				'send_password_email' => false,
				'delete' => false,
			],
			'patients' => [
				'create' => true,
				'view' => true,
				'edit' => true,
				'index' => true,
				'import_from_excel' => true
			],
			'booking' => [
				'index' => true,
				'create' => true,
				'view' => true,
				'edit' => true,
				'delete' => true,
				'edit_assigned_users' => true
			],
			'operative_reports' => [
				'index' => true,
				'index_surgeons' => true,
				'view' => true,
				'edit' => false,
				'reopen' => false,
				'create' => false,
				'print' => true,
				'archive' => true,
				'dragon_dictation' => true,
				'sign' => false,
			],
			'surgeon_templates' => [
				'index' => true,
				'view' => true,
				'edit' => false,
				'create' => false,
			],
			'site_template' => [
				'edit' => false,
			],
			'schedule' => [
				'view_settings' => true,
				'index' => true
			],
			'organization' => [
				'create' => false,
				'view' => false,
				'edit' => false
			],
			'sites' => [
				'view' => true,
				'edit' => false,
				'create' => false,
			],
			'vendors' => [
				'view' => true,
				'create' => true,
				'edit' => true
			],
			'databases' => [
				'view' => true
			],
			'insurance' => [
				'view' => true
			],
			'forms' => [
				'view' => true
			],
			'registration' => [
				'index' => true,
				'view' => true,
				'edit' => true
			],
			'case_management' => [
				'view' => true,
				'view_appointment_buttons' => true
			],
			'case_management_intake' => [
				'view' => true
			],
			'case_management_clinical' => [
				'view' => true,
				'view_hp' => true,
				'view_pre_op' => true,
				'view_op' => true,
				'view_post_op' => true,
				'view_op_report' => true,
				'view_discharge' => true,
				'view_inventory' => true,
				'pre_populate_pref_card' => true
			],
			'case_management_item_log' => [
				'view' => true
			],
			'case_management_time_log' => [
				'view' => true
			],
			'case_management_audit' => [
				'view' => true
			],
			'patient-portal' => [
				'settings' => false,
				'send_login_email' => true
			],
			'verifications' => [
				'index' => true,
				'view' => true
			],
			'sms-template' => [
				'index' => true
			],
			'alerts' => [
				'settings' => true
			],
			'booking_sheet_template' => [
				'view' => true,
				'edit' => true,
				'create' => true,
				'delete' => true
			],
			'efax' => [
				'view' => true
			],
			'dashboard' => [
				'index' => true,
			],
			'financial_documents' => [
				'index' => true,
			],
		],
	]
];