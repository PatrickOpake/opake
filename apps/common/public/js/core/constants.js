// Filters
(function (opakeCore, angular) {
	'use strict';

	opakeCore.constant('PatientConst', {
		TITLES: {
			0: '',
			1: 'Mr.',
			2: 'Ms',
			3: 'Miss',
			4: 'Mrs.',
			5: 'Dr.',
			6: 'Hon',
			7: 'Rev',
			8: 'Pvt',
			9: 'Cpl',
			10: 'Sgt',
			11: 'Maj',
			12: 'Capt',
			13: 'Cmdr',
			14: 'Lt',
			15: 'Lt Col',
			16: 'Col',
			17: 'Gen'
		},
		SUFFIXES: {
			1: 'I',
			2: 'II',
			3: 'III',
			4: 'IV',
			5: 'Jr.',
			6: 'Sr.',
			7: 'M.D.',
			8: 'Esq.'
		},
		GENDERS: {
			1: 'Male',
			2: 'Female',
			3: 'Transgender',
			4: 'Unknown'
		},
		RACES: {
			1: 'American Indian or Alaskan Native',
			2: 'Asian',
			3: 'African American',
			4: 'Native Hawaiian or Other Pacific Islander',
			5: 'White',
			6: 'Patient Declined to Comment'
		},
		ETHNICITIES: {
			1: 'Hispanic or Latino',
			2: 'Not Hispanic or Latino',
			3: 'Patient Declined to Comment'
		},
		STATUSES_MARITAL: {
			1: 'Single',
			2: 'Married',
			3: 'Widowed',
			4: 'Divorced',
			5: 'Other'
		},
		STATUSES_EMPLOYMENT: {
			1: 'Employed',
			2: 'Full-Time Student',
			3: 'Part-Time Student',
			4: 'Retired',
			5: 'Unemployed'
		},
		INSURANCE_TITLES: [
			'Primary Insurance',
			'Secondary Insurance',
			'Tertiary Insurance',
			'Quaternary Insurance',
			'Other Insurance',
			'Other Insurance',
			'Other Insurance',
			'Other Insurance',
			'Other Insurance',
			'Other Insurance'
		],
		INSURANCE_TYPES: {
			1: 'Commercial',
			2: 'Medicare',
			3: 'Medicaid',
			4: 'No-Fault',
			5: 'Self-Pay',
			6: 'Workers Comp',
			7: 'Other',
			8: 'Auto Accident / No-Fault',
			9: 'LOP',
			10: 'Tricare',
			11: 'CHAMPVA',
			12: 'FECA Black Lung'
		},
		INSURANCE_TYPE_OPTIONS: [
			{
				id: '0',
				name: ''
			},
			{
				id: '1',
				name: 'Commercial'
			},
			{
				id: '2',
				name: 'Medicare'
			},
			{
				id: '3',
				name: 'Medicaid'
			},
			{
				id: '5',
				name: 'Self-Pay'
			},
			{
				id: '6',
				name: 'Workers Comp'
			},
			{
				id: '8',
				name: 'Auto Accident / No-Fault'
			},
			{
				id: '9',
				name: 'LOP'
			},
			{
				id: '10',
				name: 'Tricare'
			},
			{
				id: '11',
				name: 'CHAMPVA'
			},
			{
				id: '12',
				name: 'FECA Black Lung'
			},
			{
				id: '7',
				name: 'Other'
			}
		],
		INSURANCE_TYPES_ID: {
			WORKERS_COMP: 6,
			AUTO_ACCIDENT: 8,
			MEDICARE: 2,
			COMMERCIAL: 1,
			MEDICAID: 3,
			NO_FAULT: 4,
			SELF_PAY: 5,
			OTHER: 7,
			LOP: 9,
			TRICARE: 10,
			CHAMPVA: 11,
			FECA_BLACK_LUNG: 12
		},
		INSURANCE_PRIMARY: {
			0: '',
			1: 'Primary',
			2: 'Secondary',
			3: 'Tertiary',
			4: 'Quaternary',
			5: 'Other'
		},
		INSURANCE_PRIMARY_OPTIONS: [
			{id: 0, name: ''},
			{id: 1, name: 'Primary'},
			{id: 2, name: 'Secondary'},
			{id: 3, name: 'Tertiary'},
			{id: 4, name: 'Quaternary'},
			{id: 5, name: 'Other'}
		],
		STATUS: {
			ACTIVE: 1,
			ARCHIVE: 0
		},
		RELATIONSHIP: {
			1: 'Spouse',
			2: 'Relative',
			3: 'Friend',
			4: 'Other'
		},
		TYPE_PHONE: {
			1: 'Home',
			2: 'Work',
			3: 'Cell',
			4: 'Other'
		},
		TYPE_PHONE_NAMES: {
			HOME: 1,
			WORK: 2,
			CELL: 3,
			OTHER: 4
		},
		INSURANCE_ACCIDENT: {
			0: 'No',
			1: 'Yes'
		},
		INSURANCE_ACCIDENT_OPTIONS: [
			{
				key: null,
				name: ''
			},
			{
				key: '1',
				name: 'Yes'
			},
			{
				key: '0',
				name: 'No'
			}
		]
	});

	opakeCore.constant('CaseRegistrationConst', {

		STATUSES: [
			'Begin',
			'Continue',
			'Complete'
		],

		RELATIONSHIP_TO_INSURED: {
			0: 'Self',
			1: 'Husband',
			2: 'Wife',
			3: 'Parent',
			4: 'Sibling',
			5: 'Child',
			6: 'Other',
			7: 'Spouse',
			8: 'Employee',
			9: 'Unknown',
			10: 'Organ Donor',
			11: 'Cadaver Donor',
			12: 'Life Partner',
			13: 'Other Relationship'
		},

		RELATIONSHIP_TO_INSURED_OPTIONS: {
			0: 'Self',
			5: 'Child',
			7: 'Spouse',
			8: 'Employee',
			9: 'Unknown',
			10: 'Organ Donor',
			11: 'Cadaver Donor',
			12: 'Life Partner',
			13: 'Other Relationship'
		},

		ADMISSION_TYPE: {
			1: 'Emergency',
			2: 'Urgent',
			3: 'Elective',
			4: 'Newborn',
			5: 'Trauma',
			9: 'Information Not Available'
		},

		POINT_OF_ORIGIN: {
			1: 'Non-Health Care Facility Point of Origin',
			2: 'Clinic or Physician Referral',
			3: 'Transfer from Hospital',
			4: 'Transfer from SNF',
			5: 'Transfer from another health care facility',
			6: 'Emergency Room',
			7: 'Court/Law Enforcement',
			8: 'Information not available',
			9: 'Transfer from one unit to another in same hospital',
			10: 'Transfer from Ambulatory Surgical Center',
			11: 'Transfer from Hospice Facility'
		},

		ANESTHESIA_TYPE: {
			0: 'Gen',
			1: 'Mac',
			2: 'IV Sed',
			3: 'Local',
			4: 'Block',
			5: 'Other',
			6: 'Not Specified'
		},

		ANESTHESIA_DRUGS: {
			0: 'Ultane',
			1: 'Fentanyl',
			2: 'Propofol',
			3: 'Midazolam',
			4: 'Other'
		},

		SPECIAL_EQUIPMENT_REQUIRED: {
			0: 'No',
			1: 'Yes'
		},

		ADMISSION_HOUR: {
			1: '1:00 a.m.',
			2: '2:00 a.m.',
			3: '3:00 a.m.',
			4: '4:00 a.m.',
			5: '5:00 a.m.',
			6: '6:00 a.m.',
			7: '7:00 a.m.',
			8: '8:00 a.m.',
			9: '9:00 a.m.',
			10: '11:00 a.m.',
			12: '12:00 p.m.',
			13: '1:00 p.m.',
			14: '2:00 p.m.',
			15: '3:00 p.m.',
			16: '4:00 p.m.',
			17: '5:00 p.m.',
			18: '6:00 p.m.',
			19: '7:00 p.m.',
			20: '8:00 p.m.',
			21: '9:00 p.m.',
			22: '10:00 p.m.',
			23: '11:00 p.m.',
			24: '12:00 a.m.'
		},

		MOBILITY: {
			1: 'Ambulatory',
			2: 'Wheelchair',
			3: 'Ambulance/Ambulette'
		},

		DOCUMENTS: [
			{field: 'assignment_of_benefits', name: 'Assignment of Benefits'},
			{field: 'advanced_beneficiary_notice', name: 'Advanced Beneficiary Notice'},
			{field: 'consent_for_treatment', name: 'Consent for Anesthesia'},
			{field: 'smoking_status', name: 'Smoking Status'},
			{field: 'hipaa_acknowledgement', name: 'HIPAA Acknowledgement'},
			{field: 'hp', name: 'H&P'}
		],

		APPOINTMENT_STATUS: {
			NEW: 0,
			CANCELED: 1,
			COMPLETED: 2
		},

		PATIENTS_RELATIONS: {
			SELF: 1,
			AUTO_ACCIDENT: 2,
			WORKERS_COMP: 3,
			NOT_APPLICABLE: 4
		},

		PATIENT_RELATIONS_LIST: {
			1: 'Self',
			2: 'Auto Accident / No-Fault',
			3: 'Workers Comp',
			4: 'Not Applicable'
		},

		CANCEL_STATUSES: {
			0: 'Patient Responsibility',
			1: 'Physician Responsibility',
			2: 'Before Anesthesia',
			3: 'After Anesthesia',
			4: 'No Show',
			5: 'Other'
		},
		LOCATION: {
			0: 'NA',
			1: 'Left',
			2: 'Right',
			3: 'Bilateral'
		},
		PRE_OP_DATA_REQUIRED: {
			0: 'None',
			1: 'Medical Clearance',
			2: 'Pre-Op Labs',
			3: 'X-Ray',
			4: 'EKG'
		},
		STUDIES_ORDERED: {
			0: 'None',
			1: 'CBC',
			2: 'CHEMS',
			3: 'EKG',
			4: 'PT/PTT',
			5: 'CXR',
			6: 'LFT’s',
			7: 'Dig Level',
			9: 'Other'
		},
		TRANSPORTATION: {
			0: 'No',
			1: 'Yes'
		},
		YES_NO: {
			0: 'No',
			1: 'Yes'
		},

		IN_SERVICE_TYPES: {
			1: 'In Service',
			2: 'Cleaning',
			3: 'Maintenance',
			4: 'Repair',
			5: 'Other'
		}

	});

	opakeCore.constant('OperativeReportTemplateConst', {
		GROUPS: {
			CASEINFO: 1,
			DESCRIPTIONS: 2,
			MATERIALS: 3,
			CONCLUSIONS: 4,
			FOLLOW_UP: 5
		},
		GROUPS_NAME: {
			1: 'Case Information',
			2: 'Descriptions',
			3: 'Materials',
			4: 'Conclusions',
			5: 'Follow Up'
		},
		FIELD_SECTIONS: {
			2: 'Descriptions',
			3: 'Materials',
			4: 'Conclusions',
			5: 'Follow Up'
		},
		TYPE_FIELDS: {
			TEXT_FIELD: 1,
			LIST: 2
		},
		TYPE_FIELDS_LIST: {
			1: 'Text Field',
			2: 'List'
		}
	});

	opakeCore.constant('DateConst', {
		MONTHS: [
			{
				id: 1,
				label: 'January',
				shortLabel: 'Jan'
			},
			{
				id: 2,
				label: 'February',
				shortLabel: 'Feb'
			},
			{
				id: 3,
				label: 'March',
				shortLabel: 'Mar'
			},
			{
				id: 4,
				label: 'April',
				shortLabel: 'Apr'
			},
			{
				id: 5,
				label: 'May',
				shortLabel: 'May'
			},
			{
				id: 6,
				label: 'June',
				shortLabel: 'Jun'
			},
			{
				id: 7,
				label: 'July',
				shortLabel: 'Jul'
			},
			{
				id: 8,
				label: 'August',
				shortLabel: 'Aug'
			},
			{
				id: 9,
				label: 'September',
				shortLabel: 'Sep'
			},
			{
				id: 10,
				label: 'October',
				shortLabel: 'Oct'
			},
			{
				id: 11,
				label: 'November',
				shortLabel: 'Nov'
			},
			{
				id: 12,
				label: 'December',
				shortLabel: 'Dec'
			}
		]
	});

	opakeCore.constant('FeeScheduleConst', {
		TYPE_FIELDS_LIST: {
			1: 'Out of Network',
			2: 'Medicare',
			3: 'Medicaid',
			4: 'Self-Pay',
			5: 'Workers Comp',
			6: 'Auto Accident/No-Fault'
		}
	});

	opakeCore.constant('options', {});

})(opakeCore, angular);
