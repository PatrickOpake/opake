// Filters
(function (opakeApp, angular) {
	'use strict';

	opakeApp.constant('InsuranceConst', {
		STATUS: {
			false: 'Inactive',
			true: 'Active'
		},
		REIMBURSEMENT : {
			1: 'UCR-Based',
			2: 'Medicare-Based'
		}
	});

	opakeApp.constant('EligibleCoverageConst', {
		TYPES: {
			0: '',
			1: 'Single',
			2: 'Family',
			3: 'Parent & Child',
			4: 'Husband & Wife'
		}
	});

	opakeApp.constant('VerificationConst', {
		STATUSES: {
			0: 'Begin',
			1: 'Continue',
			2: 'Completed'
		}
	});

	opakeApp.constant('CardConst', {
		ITEM_STATUSES: {
			UNDEFINED: 0,
			COMPLETED: 1,
			MOVED: 2
		},
		CARD_STATUSES: {
			STATUS_OPEN: 1,
			STATUS_DRAFT: 2,
			STATUS_SUBMITTED: 3
		},
		STATUSES: {
			1: 'Begin',
			2: 'Continue',
			3: 'Completed'
		}
	});

	opakeApp.constant('CodingConst', {
		DIAGNOSIS_ROWS: [
			{id : 1, title: 'A'},
			{id : 2, title: 'B'},
			{id : 3, title: 'C'},
			{id : 4, title: 'D'},
			{id : 5, title: 'E'},
			{id : 6, title: 'F'},
			{id : 7, title: 'G'},
			{id : 8, title: 'H'},
			{id : 9, title: 'I'},
			{id : 10, title: 'J'},
			{id : 11, title: 'K'},
			{id : 12, title: 'L'}
		],
		BILL_TYPE: {
			0: 'Non-Payment / Zero Claim',
			1: 'Admit Through Discharge Date',
			2: 'First Interim Claim',
			3: 'Continuing Interim Claim',
			4: 'Last Interim Claim',
			5: 'Late Charge(s) of Prior Claim',
			6: 'Replacement of Prior Claim',
			7: 'Void / Cancel of Prior Claim'
		},
		SUPPLY_TYPES: {
			0: 'UNITS'
		},
		PROCEDURE_TYPES: {
			0: 'Null'
		}
	});

	opakeApp.constant('CalendarConst', {
		COLORS: [
			{name: 'Purple', key: 'purple'},
			{name: 'Sky Blue', key: 'sky-blue'},
			{name: 'Apricot', key: 'apricot'},
			{name: 'Aquamarine', key: 'aquamarine'},
			{name: 'Gold Sand', key: 'gold-sand'},
			{name: 'Grey', key: 'grey'},
			{name: 'Aqua', key: 'aqua'},
			{name: 'Azure', key: 'azure'},
			{name: 'Beige', key: 'beige'},
			{name: 'Bisque', key: 'bisque'},
			{name: 'Blue', key: 'blue'},
			{name: 'Blue Violet', key: 'blue-violet'},
			{name: 'Coral', key: 'coral'},
			{name: 'Forest Green', key: 'forest-green'},
			{name: 'Indian Red', key: 'indian-red'},
			{name: 'Lavender', key: 'lavender'},
			{name: 'Green Yellow', key: 'green-yellow'},
			{name: 'Light Pink', key: 'light-pink'},
			{name: 'Light Sea Green', key: 'light-sea-green'},
			{name: 'Orange', key: 'orange'},
			{name: 'Orchid', key: 'orchid'},
			{name: 'Pale Green', key: 'pale-green'},
			{name: 'Teal', key: 'teal'},
			{name: 'Thistle', key: 'thistle'},
			{name: 'Default Grey', key: 'default-grey'}
		],
		DEFAULT_COLOR: 'default-grey'
	});

	opakeApp.constant('CaseSettingConst', {
		BLOCK_TIMING: {
			1: 'Never',
			2: '12 Hours',
			3: '24 Hours',
			4: '48 Hours',
			5: '96 Hours'
		}
	});

	opakeApp.constant('CaseBlockingConst', {
		DURATION: {
			1: '15 min',
			2: '30 min',
			3: '1 hour',
			4: '1.5 hour',
			5: '2 hour',
			6: '3 hour',
			7: '4 hour',
			8: '6 hour',
			9: 'all day'
		},
		RECURRENCE: {
			1: 'Daily',
			2: 'Weekly',
			3: 'Monthly'
		},
		MONTHLY_DAYS: {
			1: '1st',
			2: '2nd',
			3: '3th',
			4: '4th',
			5: '5th',
			6: '6th',
			7: '7th',
			8: '8th',
			9: '9th',
			10: '10th',
			11: '11th',
			12: '12th',
			13: '13th',
			14: '14th',
			15: '15th',
			16: '16th',
			17: '17th',
			18: '18th',
			19: '19th',
			20: '20th',
			21: '21st',
			22: '22nd',
			23: '23rd',
			24: '24th',
			25: '25th',
			26: '26th',
			27: '27th',
			28: '28th',
			29: '29th',
			30: '30th',
			31: '31st'
		},
		WEEKLY_DAYS: {
			1: 'Monday',
			2: 'Tuesday',
			3: 'Wednesday',
			4: 'Thursday',
			5: 'Friday',
			6: 'Saturday',
			7: 'Sunday'
		},
		SHORT_WEEKLY_DAYS: {
			1: 'Mo',
			2: 'Tu',
			3: 'We',
			4: 'Th',
			5: 'Fr',
			6: 'Sa',
			7: 'Su'
		},
		NUMBER_OF_WEEK: {
			1: '1st',
			2: '2nd',
			3: '3rd',
			4: '4th',
			5: 'Last'
		},
		NUMBER_OF_MONTH_WEEK: {
			1: 'First',
			2: 'Second',
			3: 'Third',
			4: 'Fourth',
			5: 'Last'
		}
	});

	opakeApp.constant('UserConst', {
		MIN_PASSWORD_LENGTH: 8,
		PROFESSION: {
			MATERIAL_MANAGER: 1,
			ADMINISTRATOR: 2,
			SURGEON: 3,
			ANESTHESIOLOGIST: 4,
			NURSE: 5,
			SCRUB_TECHNOLOGIST: 6,
			PHYSICIAN_ASSISTANT: 7,
			NURSE_ANESTHETIST: 8,
			NURSE_PRACTITIONER: 19,
			CHIROPRACTOR: 20,
			DICTATION: 21,
			BILLER: 22
		},
		ROLES: {
			FULL_ADMIN: 1,
			FULL_CLINICAL: 3,
			DOCTOR: 5,
			SATELLITE_OFFICE: 7,
			DICTATION: 9,
			BILLER: 11,
			SCHEDULER: 13
		},
		PHONE_TYPES: {
			0: '',
			1: 'Home',
			2: 'Cell',
			3: 'Office',
			4: 'Other'
		},
		ADDRESS_TYPES: {
			0: '',
			1: 'Home',
			2: 'Office',
			4: 'Other',
			5: 'PO Box'
		},
		ADDRESS_TYPE_OPTIONS: [
			{id: '0', title: ''},
			{id: '1', title: 'Home'},
			{id: '2', title: 'Office'},
			{id: '5', title: 'PO Box'},
			{id: '4', title: 'Other'}
		]
	});

	opakeApp.constant('CMConst', {
		STAGES: {
			intake: 'Intake',
			clinical: 'Clinical',
			billing: 'Billing',
			item_log: 'Item Log',
			time_log: 'Time Log',
			audit: 'Audit'
		},
		PHASES: {
			intake: {
				case_details: 'Case Details',
				pre_operative_questionnaire: 'Interview',
				medications_and_allergies: 'Medications & Allergies',
				influenza: 'Influenza',
				charts: 'Charts',
				verification: 'Verification & Pre-Authorization'
			},
			clinical: {
				//pre_op: 'Pre-Op',
				operation: 'Operation',
				//post_op: 'Post-Op',
				report: 'Operative Report',
				discharge: 'Discharge'
			},
			item_log: {
				preference_card: 'Preference Card',
				//materials_report: 'Materials Report'
			}
		}
	});

	opakeApp.constant('FormDocumentsConst', {
		SEGMENTS: [
			{
				NAME: 'Intake',
				KEY: 'intake',
				REQUIRED_FORMS: [
					'assignment_of_benefits',
					'advanced_beneficiary_notice',
					'consent_for_treatment',
					'smoking_status',
					'medical_history',
					'hipaa_acknowledgement',
					'hp'
				]
			},
			{
				NAME: 'Billing',
				KEY: 'billing'
			}
		],

		TYPE_OF_FORMS: {
			intake: {
				assignment_of_benefits: 'Assignment of Benefits',
				advanced_beneficiary_notice: 'Advanced Beneficiary Notice',
				consent_for_treatment: 'Consent for Treatment',
				smoking_status: 'Smoking Status',
				medical_history: 'Medical History',
				hipaa_acknowledgement: 'HIPAA Acknowledgement',
				hp: 'H&P',
				other: 'Other'
			},
			billing: {
				other: 'Other'
			}
		},
		DYNAMIC_OPTIONS: [
			{key: 'patient_first_name', title: 'Patient First Name'},
			{key: 'patient_last_name', title: 'Patient Last Name'},
			{key: 'patient_full_name_first', title: 'Patient FullName First'},
			{key: 'patient_full_name_last', title: 'Patient FullName Last'},
			{key: 'patient_account', title: 'Patient Account #'},
			{key: 'patient_age', title: 'Patient Age'},
			{key: 'patient_dob', title: 'Patient Date of Birth'},
			{key: 'patient_gender', title: 'Patient Gender (Male/Female)'},
			{key: 'patient_pronoun', title: 'Patient Gender (He/She)'},
			{key: 'patient_address', title: 'Patient Street Address'},
			{key: 'patient_apt', title: 'Patient Apt #'},
			{key: 'patient_city', title: 'Patient City'},
			{key: 'patient_state', title: 'Patient State'},
			{key: 'patient_country', title: 'Patient Country'},
			{key: 'patient_zip', title: 'Patient ZIP'},
			{key: 'patient_mrn', title: 'Patient MRN'},
			{key: 'physician_name', title: 'Physician Name'},
			{key: 'dos', title: 'DOS'},
			{key: 'primary_insurance', title: 'Primary Insurance Co.'},
			{key: 'site_name', title: 'Site Name'},
			{key: 'site_address', title: 'Site Address'},
			{key: 'site_city', title: 'Site City'},
			{key: 'site_state', title: 'Site State'},
			{key: 'site_country', title: 'Site Country'},
			{key: 'site_zip', title: 'Site Zip'},
			{key: 'site_phone', title: 'Site Phone'}
		]
	});

	opakeApp.constant('OperativeReportConst', {
		STATUSES: {
			open: 1,
			draft: 2,
			submitted: 3,
			signed: 4
		},
		STATUSES_NAME: {
			1: 'Begin',
			2: 'Continue',
			3: 'Edit',
			4: 'Amend',
			5: 'Edit',
			null: 'Edit'
		}
	});

	opakeApp.constant('BillingConst', {
		STATUSES: {
			1: 'Begin',
			2: 'Continue',
			3: 'Submitted',
			4: 'Ready'
		},
		MANUAL_BILLING_STATUSES: {
			0: '',
			8: 'TBP',
			9: 'APP',
			10: 'ARB',
			11: 'NOONB',
			12: 'BEX',
			13: 'COLL',
			14: 'CRB',
			15: 'CD',
			16: 'CP',
			17: 'CLX',
			18: 'SECINS'
		},
		MANUAL_BILLING_STATUSES_DESC: {
			0: '',
			8: 'TRANSFERRED BALANCE TO PATIENT',
			9: 'APPEAL',
			10: 'ARBITRATION',
			11: 'NO OUT OF NETWORK BENEFITS',
			12: 'BENEFITS EXHUASTED',
			13: 'COLLECTION YES',
			14: 'CLAIM RESUBMISSION',
			15: 'CLAIM DENIED',
			16: 'CLAIM IS PROCESSING',
			17: 'CLAIM CLOSED',
			18: 'SUBMITTED TO SECONDARY INSURANCE',
		},
		CLAIM_STATUSES: {
			pending: 'Pending',
			coding: 'Coding Required',
			billing: 'Ready To Bill'
		},
		NAV_CLAIM_STATUSES: {
			'NEW': 0,
			'SENT': 1,
			'ACCEPTED_BY_PROVIDER': 3,
			'ACCEPTED_BY_PAYOR': 5,
			'REJECTED_BY_PROVIDER': 2,
			'REJECTED_BY_PAYOR': 4,
			'PASSED_PROVIDER_VALIDATION': 6,
			'PASSED_PAYOR_VALIDATION': 7,
			'PAYMENT_DENIED': 8,
			'PAYMENT_PROCESSED': 9
		},
		NAV_CLAIM_ACK_STATUSES: {
			STATUS_APPROVED: 1,
			STATUS_REJECTED: 2
		},
		NAV_CLAIM_STATUS_OPTIONS: [
			{id: 1, title: 'Sent'},
			{id: 3, title: 'Accepted by provider'},
			{id: 5, title: 'Accepted by payer'},
			{id: 2, title: 'Rejected by provider'},
			{id: 4, title: 'Rejected by payer'},
			{id: 8, title: 'Payment denied'},
			{id: 9, title: 'Payment processed'}
		],
		NAV_CLAIM_ELECTRONIC_TYPE_OPTIONS: [
			{id: null, title: ''},
			{id: 3, title: 'Electronic UB04'},
			{id: 4, title: 'Electronic 1500'}
		],
		NAV_TRANSACTION_LIST_OPTIONS: [
			{id: null, title: ''},
			{id: 1, title: '837-P'},
			{id: 5, title: '837-I'},
			{id: 2, title: '997'},
			{id: 3, title: '277'},
			{id: 4, title: '835'},
			{id: -1, title: 'Unknown'}
		],
		CLAIMS_PROCESSING_PAYMENT_STATUS_OPTIONS: [
			{id: 1, title: 'Ready To Post'},
			{id: 2, title: 'On Hold'},
			{id: 3, title: 'Exception'}
		],
		LEDGER_PAYMENT_METHOD_OPTIONS: [
			{id: null, title: ''},
			{id: 1, title: 'Cash'},
			{id: 2, title: 'Check'},
			{id: 3, title: 'Credit Card'},
			{id: 4, title: 'Debit Card'},
			{id: 5, title: 'Electronic'}
		],
		LEDGER_PAYMENT_METHODS: {
			CASH: 1,
			CHECK: 2,
			CREDIT_CARD: 3,
			DEBIT_CARD: 4,
			ELECTRONIC: 5
		},
		LEDGER_PAYMENT_SOURCES: {
			INSURANCE: 1,
			PATIENT_CO_PAY: 2,
			PATIENT_DEDUCTIBLE: 3,
			PATIENT_CO_INSURANCE: 4,
			PATIENT_OOP: 5,
			ADJUSTMENT: 6,
			WRITE_OFF: 7,
			WRITE_OFF_CO_PAY: 8,
			WRITE_OFF_CO_INSURANCE: 9,
			WRITE_OFF_DEDUCTIBLE: 10,
			WRITE_OFF_OOP: 11
		},
		LEDGER_PAYMENT_ACTIVITY_SOURCE_OPTIONS: [
			{id: null, title: ''},
			{id: 1, title: 'Insurance'},
			{id: 2, title: 'Patient'},
			{id: 3, title: 'Adjustment'},
			{id: 4, title: 'Write Off'}
		],
		LEDGER_PAYMENT_TYPE_DESCRIPTIONS: {
			1: 'Patient Payment',
			2: 'Insurance Payment',
			3: 'Insurance Adjustment',
			4: 'Patient Copayment',
			5: 'Write Off',
			6: 'Custom'
		},
		CLAIM_TYPES: {
			1: 'UB04',
			2: '1500'
		},
		COLLECTION_CLAIM_TYPES: {
			1: 'Paper UB04',
			2: 'Paper 1500',
			3: 'Electronic UB04',
			4: 'Electronic 1500'
		},
	});

	opakeApp.constant('VendorConst', {
		TYPES: {
			dist: 'Distributor',
			manf: 'Manufacturer'
		}
	});

	opakeApp.constant('TimeLogConst', {
		STAGES: [
			{name: 'Facility Arrival', code:'facility_arrival'},
			{name: 'Pre-Op Arrival', code: 'pre_op_arrival'},
			{name: 'Pre-Op Exit', code: 'pre_op_exit'},
			{name: 'Enter OR', code: 'enter_or'},
			{name: 'Anesthesia Start', code: 'anesthesia_start'},
			{name: 'Incision', code: 'incision'},
			{name: 'Closure', code: 'closure'},
			{name: 'Anesthesia Finish', code: 'anesthesia_finish'},
			{name: 'Exit OR', code: 'operation_room_exit'},
			{name: 'Post-Op Exit', code: 'post_op_exit'},
			{name: 'Facility Discharge', code: 'facility_discharge'}
		]
	});

	opakeApp.constant('BookingConst', {
		STATUSES: {
			0: 'Not Scheduled',
			1: 'Scheduled'
		},
		TEMPLATE_FIELDS: {
			 FIELD_PATIENT_NAME: 1,
			 FIELD_MI: 2,
			 FIELD_SUFFIX: 3,
			 FIELD_IF_MINOR: 4,
			 FIELD_ADDRESS: 5,
			 FIELD_APT: 6,
			 FIELD_STATE: 7,
			 FIELD_CITY: 8,
			 FIELD_ZIP: 9,
			 FIELD_COUNTRY: 10,
			 FIELD_PHONE: 11,
			 FIELD_ADDITIONAL_PHONE: 12,
			 FIELD_EMAIL: 13,
			 FIELD_DATE_OF_BIRTH: 14,
			 FIELD_SSN: 15,
			 FIELD_GENDER: 16,
			 FIELD_MARTIAL_STATUS: 17,
			 FIElD_EMERGENCY_CONTACT_RELATIONSHIP: 18,
			 FIELD_EMERGENCY_PHONE: 19,

			 FIELD_SURGEON: 20,
			 FIELD_SURGEON_ASSISTANT: 21,
			 FIELD_OTHER_STAFF: 22,
			 FIELD_PRIOR_AUTHORIZATION_NUMBER: 23,
			 FIELD_ADMISSION_TYPE: 24,
			 FIELD_ROOM: 25,
			 FIELD_POINT_OF_ORIGIN: 26,
			 FIELD_DATE_OF_SERVICE: 27,
			 FIELD_TIME_START: 28,
			 FIELD_CASE_LENGTH: 29,
			 FIELD_PATIENT_EMPLOYED: 30,
			 FIELD_PROPOSED_PROCEDURE_CODES: 31,
			 FIELD_LOCATION: 32,
			 FIELD_DATE_OF_INJURY: 33,
			 FIELD_PRIMARY_DIAGNOSIS: 34,
			 FIELD_SECONDARY_DIAGNOSIS: 35,
			 FIELD_PRE_OP_DATA_REQUIRED: 36,
			 FIELD_STUDIES_ORDERED: 37,
			 FIELD_ANESTHESIA_TYPE: 38,
			 FIELD_SPECIAL_EQUIPMENT: 39,
			 FIELD_TRANSPORT: 40,
			 FIELD_IMPLANTS: 41,
			 FIELD_DESCRIPTION: 42,
			 FIELD_POINT_OF_ORIGIN_NPI: 43,
			 FIELD_POINT_OF_ORIGIN_PROVIDER: 44
		}
	});

	opakeApp.constant('InventoryConst', {
		COMPLETE_STATUS: {
			0: 'Incomplete',
			1: 'Complete'
		}
	});

	opakeApp.constant('ProcedureConst', {
		STATUSES: {
			0: 'Inactive',
			1: 'Active'
		}
	});

	opakeApp.constant('EligibleNavicureConst', {
		GENDER: {
			F: 'Female',
			M: 'Male',
			U: 'Unknown'
		},
		ENTITY_IDENTIFIER_CODE_INSURANCE : {
			'2B': 'Third-Party Administrator',
			'36': 'Employer',
			'GP': 'Gateway Provider',
			'P5': 'Plan Sponsor',
			'PR': 'Payer'
		},
		ENTITY_TYPE_QUALIFIER : {
			'1': 'Person',
			'2': 'Non-Person Entity'
		},
		ENTITY_IDENTIFIER_CODE_PROVIDER : {
			'1P': 'Provider',
			'2B': 'Third-Party Administrator',
			'36': 'Employer',
			'80': 'Hospital',
			'FA': 'Facility',
			'GP': 'Gateway Provider',
			'P5': 'Plan Sponsor',
			'PR': 'Payer'
		},
		ELIGIBILITY_INFO_CODE: {
			'1' : 'Active Coverage',
			'2' : 'Active - Full Risk Capitation',
			'3' : 'Active - Services Capitated',
			'4' : 'Active - Services Capitated to Primary Care Physician',
			'5' : 'Active - Pending Investigation',
			'6' : 'Inactive',
			'7' : 'Inactive - Pending Eligibility Update',
			'A' : 'Co-Insurance',
			'B' : 'Co-Payment',
			'C' : 'Deductible',
			'D' : 'Benefit Description',
			'E' : 'Exclusions',
			'F' : 'Limitations',
			'G' : 'Out of Pocket (Stop Loss)',
			'H' : 'Unlimited',
			'I' : 'Non-Covered',
			'J' : 'Cost Containment',
			'K' : 'Reserve',
			'L' : 'Primary Care Provider',
			'M' : 'Pre-existing Condition',
			'MC': 'Managed Care Coordinator',
			'N' : 'Services Restricted to Following Provider',
			'O' : 'Not Deemed a Medical Necessity',
			'P' : 'Benefit Disclaimer',
			'Q' : 'Second Surgical Opinion Required',
			'R' : 'Other or Additional Payor',
			'S' : 'Prior Year(s) History',
			'T' : 'Card(s) Reported Lost/Stolen',
			'U' : 'Contact Following Entity for Eligibility or Benefit Information',
			'V' : 'Cannot Process',
			'W' : 'Other Source of Data',
			'X' : 'Health Care Facility',
			'Y' : 'Spend Down'
		},
		COVERAGE_LEVEL_CODE: {
			'CHD': 'Children Only',
			'DEP': 'Dependents Only',
			'ECH': 'Employee and Children',
			'EMP': 'Employee Only',
			'ESP': 'Employee and Spouse',
			'FAM': 'Family',
			'IND': 'Individual',
			'SPC': 'Spouse and Children',
			'SPO': 'Spouse Only'
		},
		SERVICE_TYPE_CODE: {
			'1': 'Medical Care',
			'2': 'Surgical',
			'3': 'Consultation',
			'4': 'Diagnostic X-Ray',
			'5': 'Diagnostic Lab',
			'6': 'Radiation Therapy',
			'7': 'Anesthesia',
			'8': 'Surgical Assistance',
			'9': 'Other Medical',
			'10': 'Blood Charges',
			'11': 'Used Durable Medical Equipment',
			'12': 'Durable Medical Equipment Purchase',
			'13': 'Ambulatory Service Center Facility',
			'14': 'Renal Supplies in the Home',
			'15': 'Alternate Method Dialysis',
			'16': 'Chronic Renal Disease (CRD) Equipment',
			'17': 'Pre-Admission Testing',
			'18': 'Durable Medical Equipment Rental',
			'19': 'Pneumonia Vaccine',
			'20': 'Second Surgical Opinion',
			'21': 'Third Surgical Opinion',
			'22': 'Social Work',
			'23': 'Diagnostic Dental',
			'24': 'Periodontics',
			'25': 'Restorative',
			'26': 'Endodontics',
			'27': 'Maxillofacial Prosthetics',
			'28': 'Adjunctive Dental Services',
			'30': 'Health Benefit Plan Coverage',
			'32': 'Plan Waiting Period',
			'33': 'Chiropractic',
			'34': 'Chiropractic Office Visits',
			'35': 'Dental Care',
			'36': 'Dental Crowns',
			'37': 'Dental Accident',
			'38': 'Orthodontics',
			'39': 'Prosthodontics',
			'40': 'Oral Surgery',
			'41': 'Routine (Preventive) Dental',
			'42': 'Home Health Care',
			'43': 'Home Health Prescriptions',
			'44': 'Home Health Visits',
			'45': 'Hospice',
			'46': 'Respite Care',
			'47': 'Hospital',
			'48': 'Hospital - Inpatient',
			'49': 'Hospital - Room and Board',
			'50': 'Hospital - Outpatient',
			'51': 'Hospital - Emergency Accident',
			'52': 'Hospital - Emergency Medical',
			'53': 'Hospital - Ambulatory Surgical',
			'54': 'Long Term Care',
			'55': 'Major Medical',
			'56': 'Medically Related Transportation',
			'57': 'Air Transportation',
			'58': 'Cabulance',
			'59': 'Licensed Ambulance',
			'60': 'General Benefits',
			'61': 'In-vitro Fertilization',
			'62': 'MRI/CAT Scan',
			'63': 'Donor Procedures',
			'64': 'Acupuncture',
			'65': 'Newborn Care',
			'66': 'Pathology',
			'67': 'Smoking Cessation',
			'68': 'Well Baby Care',
			'69': 'Maternity',
			'70': 'Transplants',
			'71': 'Audiology Exam',
			'72': 'Inhalation Therapy',
			'73': 'Diagnostic Medical',
			'74': 'Private Duty Nursing',
			'75': 'Prosthetic Device',
			'76': 'Dialysis',
			'77': 'Otological Exam',
			'78': 'Chemotherapy',
			'79': 'Allergy Testing',
			'80': 'Immunizations',
			'81': 'Routine Physical',
			'82': 'Family Planning',
			'83': 'Infertility',
			'84': 'Abortion',
			'85': 'AIDS',
			'86': 'Emergency Services',
			'87': 'Cancer',
			'88': 'Pharmacy',
			'89': 'Free Standing Prescription Drug',
			'90': 'Mail Order Prescription Drug',
			'91': 'Brand Name Prescription Drug',
			'92': 'Generic Prescription Drug',
			'93': 'Podiatry',
			'94': 'Podiatry - Office Visits',
			'95': 'Podiatry - Nursing Home Visits',
			'96': 'Professional (Physician)',
			'97': 'Anesthesiologist',
			'98': 'Professional (Physician) Visit - Office',
			'99': 'Professional (Physician) Visit - Inpatient',
			'A0': 'Professional (Physician) Visit - Outpatient',
			'A1': 'Professional (Physician) Visit - Nursing Home',
			'A2': 'Professional (Physician) Visit - Skilled Nursing Facility',
			'A3': 'Professional (Physician) Visit - Home',
			'A4': 'Psychiatric',
			'A5': 'Psychiatric - Room and Board',
			'A6': 'Psychotherapy',
			'A7': 'Psychiatric - Inpatient',
			'A8': 'Psychiatric - Outpatient',
			'A9': 'Rehabilitation',
			'AA': 'Rehabilitation - Room and Board',
			'AB': 'Rehabilitation - Inpatient',
			'AC': 'Rehabilitation - Outpatient',
			'AD': 'Occupational Therapy',
			'AE': 'Physical Medicine',
			'AF': 'Speech Therapy',
			'AG': 'Skilled Nursing Care',
			'AH': 'Skilled Nursing Care - Room and Board',
			'AI': 'Substance Abuse',
			'AJ': 'Alcoholism',
			'AK': 'Drug Addiction',
			'AL': 'Vision (Optometry)',
			'AM': 'Frames',
			'AN': 'Routine Exam',
			'AO': 'Lenses',
			'AQ': 'Nonmedically Necessary Physical',
			'AR': 'Experimental Drug Therapy',
			'B1': 'Burn Care',
			'B2': 'Brand Name Prescription Drug - Formulary',
			'B3': 'Brand Name Prescription Drug - Non-Formulary',
			'BA': 'Independent Medical Evaluation',
			'BB': 'Partial Hospitalization (Psychiatric)',
			'BC': 'Day Care (Psychiatric)',
			'BD': 'Cognitive Therapy',
			'BE': 'Massage Therapy',
			'BF': 'Pulmonary Rehabilitation',
			'BG': 'Cardiac Rehabilitation',
			'BH': 'Pediatric',
			'BI': 'Nursery',
			'BJ': 'Skin',
			'BK': 'Orthopedic',
			'BL': 'Cardiac',
			'BM': 'Lymphatic',
			'BN': 'Gastrointestinal',
			'BP': 'Endocrine',
			'BQ': 'Neurology',
			'BR': 'Eye',
			'BS': 'Invasive Procedures',
			'BT': 'Gynecological',
			'BU': 'Obstetrical',
			'BV': 'Obstetrical/Gynecological',
			'BW': 'Mail Order Prescription Drug: Brand Name',
			'BX': 'Mail Order Prescription Drug: Generic',
			'BY': 'Physician Visit - Office: Sick',
			'BZ': 'Physician Visit - Office: Well',
			'C1': 'Coronary Care',
			'CA': 'Private Duty Nursing - Inpatient',
			'CB': 'Private Duty Nursing - Home',
			'CC': 'Surgical Benefits - Professional (Physician)',
			'CD': 'Surgical Benefits - Facility',
			'CE': 'Mental Health Provider - Inpatient',
			'CF': 'Mental Health Provider - Outpatient',
			'CG': 'Mental Health Facility - Inpatient',
			'CH': 'Mental Health Facility - Outpatient',
			'CI': 'Substance Abuse Facility - Inpatient',
			'CJ': 'Substance Abuse Facility - Outpatient',
			'CK': 'Screening X-ray',
			'CL': 'Screening laboratory',
			'CM': 'Mammogram, High Risk Patient',
			'CN': 'Mammogram, Low Risk Patient',
			'CO': 'Flu Vaccination',
			'CP': 'Eyewear and Eyewear Accessories',
			'CQ': 'Case Management',
			'DG': 'Dermatology',
			'DM': 'Durable Medical Equipment',
			'DS': 'Diabetic Supplies',
			'GF': 'Generic Prescription Drug - Formulary',
			'GN': 'Generic Prescription Drug - Non-Formulary',
			'GY': 'Allergy',
			'IC': 'Intensive Care',
			'MH': 'Mental Health',
			'NI': 'Neonatal Intensive Care',
			'ON': 'Oncology',
			'PT': 'Physical Therapy',
			'PU': 'Pulmonary',
			'RN': 'Renal',
			'RT': 'Residential Psychiatric Treatment',
			'TC': 'Transitional Care',
			'TN': 'Transitional Nursery Care',
			'UC': 'Urgent Care'
		},
		INSURANCE_TYPE_CODE: {
			'12' : 'Medicare Secondary Working Aged Beneficiary or Spouse with Employer Group Health Plan',
			'13' : 'Medicare Secondary End-Stage Renal Disease Beneficiary in the Mandated Coordination Period with an Employer’s Group Health Plan',
			'14' : 'Medicare Secondary, No-fault Insurance including Auto is Primary',
			'15' : 'Medicare Secondary Worker’s Compensation',
			'16' : 'Medicare Secondary Public Health Service (PHS)or Other Federal Agency',
			'41' : 'Medicare Secondary Black Lung',
			'42' : 'Medicare Secondary Veteran’s Administration',
			'43' : 'Medicare Secondary Disabled Beneficiary Under Age 65 with Large Group Health Plan (LGHP)',
			'47' : 'Medicare Secondary, Other Liability Insurance is Primary',
			'AP' : 'Auto Insurance Policy',
			'C1' : 'Commercial',
			'CO' : 'Consolidated Omnibus Budget Reconciliation Act (COBRA)',
			'CP' : 'Medicare Conditionally Primary',
			'D' : 'Disability',
			'DB' : 'Disability Benefits',
			'EP' : 'Exclusive Provider Organization',
			'FF' : 'Family or Friends',
			'GP' : 'Group Policy',
			'HM' : 'Health Maintenance Organization (HMO)',
			'HN' : 'Health Maintenance Organization (HMO) - Medicare Risk',
			'HS' : 'Special Low Income Medicare Beneficiary',
			'IN' : 'Indemnity',
			'IP' : 'Individual Policy',
			'LC' : 'Long Term Care',
			'LD' : 'Long Term Policy',
			'LI' : 'Life Insurance',
			'LT' : 'Litigation',
			'MA' : 'Medicare Part A',
			'MB' : 'Medicare Part B',
			'MC' : 'Medicaid',
			'MH' : 'Medigap Part A',
			'MI' : 'Medigap Part B',
			'MP' : 'Medicare Primary',
			'OT' : 'Other',
			'PE' : 'Property Insurance - Personal',
			'PL' : 'Personal',
			'PP' : 'Personal Payment (Cash - No Insurance)',
			'PR' : 'Preferred Provider Organization (PPO)',
			'PS' : 'Point of Service (POS)',
			'QM' : 'Qualified Medicare Beneficiary',
			'RP' : 'Property Insurance - Real',
			'SP' : 'Supplemental Policy',
			'TF' : 'Tax Equity Fiscal Responsibility Act (TEFRA)',
			'WC' : 'Workers Compensation',
			'WU' : 'Wrap Up Policy'
		},
		TIME_PERIOD_QUALIFIER: {
			'6' : 'Hour',
			'7' : 'Day',
			'13' : '24 Hours',
			'21' : 'Years',
			'22' : 'Service Year',
			'23' : 'Calendar Year',
			'24' : 'Year to Date',
			'25' : 'Contract',
			'26' : 'Episode',
			'27' : 'Visit',
			'28' : 'Outlier',
			'29' : 'Remaining',
			'30' : 'Exceeded',
			'31' : 'Not Exceeded',
			'32' : 'Lifetime',
			'33' : 'Lifetime Remaining',
			'34' : 'Month',
			'35' : 'Week',
			'36' : 'Admission'
		},
		QUANTITY_QUALIFIER: {
			'8H' : 'Minimum',
			'99' : 'Quantity Used',
			'CA' : 'Covered - Actual',
			'CE' : 'Covered - Estimated',
			'D3' : 'Number of Co-insurance Days',
			'DB' : 'Deductible Blood Units',
			'DY' : 'Days',
			'HS' : 'Hours',
			'LA' : 'Life-time Reserve - Actual',
			'LE' : 'Life-time Reserve - Estimated',
			'M2' : 'Maximum',
			'MN' : 'Month',
			'P6' : 'Number of Services or Procedures',
			'QA' : 'Quantity Approved',
			'S7' : 'Age, High Value',
			'S8' : 'Age, Low Value',
			'VS' : 'Visits',
			'YY' : 'Years'
		},
		YES_NO_CONDITION: {
			'N' : 'No',
			'U' : 'Unknown',
			'Y' : 'Yes',
			'W' : 'Not Applicable'
		},
		PRODUCT_ID_QUALIFIER: {
			'AD' : 'American Dental Association Codes',
			'CJ' : 'Current Procedural Terminology (CPT) Codes',
			'HC' : 'Health Care Financing Administration Common Procedural Coding System (HCPCS) Codes',
			'ID' : 'International Classification of Diseases, 9th Revision, Clinical Modification (ICD-9-CM) - Procedure',
			'IV' : 'Home Infusion EDI Coalition (HIEC) Product/Service Code',
			'N4' : 'National Drug Code in 5-4-2 Format',
			'ZZ' : 'Mutually Defined'
		},
		RECEIVER_ADDITIONAL_IDENTIFICATION: {
			'0B' : 'State License Number',
			'1C' : 'Medicare Provider Number',
			'1D' : 'Medicaid Provider Number',
			'1J' : 'Facility ID Number',
			'4A' : 'Personal Identification Number (PIN)',
			'CT' : 'Contract Number',
			'EL' : 'Electronic device pin number',
			'EO' : 'Submitter Identification Number',
			'HPI': 'Centers for Medicare and Medicaid Services National Provider Identifier',
			'JD' : 'User Identification',
			'N5' : 'Provider Plan Network Identification Number',
			'N7' : 'Facility Network Identification Number',
			'Q4' : 'Prior Identifier Number',
			'SY' : 'Social Security Number',
			'TJ' : 'Federal Taxpayer’s Identification Number'
		}

	});

	opakeApp.constant('NotesConst', {
		TYPES: {
			TYPE_NOTE_CASES: 1,
			TYPE_NOTE_BILLING: 2,
			TYPE_NOTE_BOOKING: 3,
			TYPE_NOTE_OP_REPORT: 4,
			TYPE_NOTE_CARD_STAFF: 5,
			TYPE_NOTE_CARD_PREF_CARD: 6,
			TYPE_NOTE_APPLIED_PAYMENT: 7
		}
	});

})(opakeApp, angular);
