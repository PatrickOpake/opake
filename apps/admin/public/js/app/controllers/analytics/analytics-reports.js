// Vendor save
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('AnalyticsReportsCtrl', [
		'$scope',
		'$http',
		'$filter',
		'View',
		'Permissions',
		'BillingConst',
		function ($scope, $http, $filter, View, Permissions, BillingConst) {

			var COLUMNS_TO_EXPORT = {
				'case_start_time': 'Scheduled Start Time',
				'case_end_time': 'Scheduled End Time',
				'case_actual_start_time': 'Actual Start Time',
				'case_actual_end_time': 'Actual End Time',
				'case_physician': 'Physician',
				'insurance_type_acronym': 'Insurance Type',
				'deductible': 'Deductible',
				'co_pay': 'Co-Pay',
				'co_insurance': 'Co-Insurance',
				'oop': 'OOP',
				'case_special_equipment': 'Special Equipment',
				'case_special_equipment_flag': 'Special Equipment (yes/no)',
				'case_implants': 'Implants',
				'case_implants_flag': 'Implants (yes/no)',
				'case_actual_duration': 'Actual Duration',
				'case_duration': 'Scheduled Duration',
				'case_procedure1': 'Procedure 1',
				'case_procedure2': 'Procedure 2',
				'case_procedure3': 'Procedure 3',
				'case_procedure4': 'Procedure 4',
				'coded_procedures': 'Coded Procedures',
				'case_description': 'Description',
				'case_date_of_service': 'Date of Service',
				'case_doctor': 'Doctor',
				'case_id': 'Case Number',
				'case_anesthesiologist': 'Anesthesiologist',
				'case_anesthesia_type': 'Anesthesia Type',
				'clinical_notes': 'Clinical Notes',
				'patient_id': 'Patient ID',
				'patient_last_name': 'Patient Last Name',
				'patient_first_name': 'Patient First Name',
				'patient_street_address': 'Address 1',
				'patient_street_address_2': 'Address 2',
				'patient_country': 'Country',
				'patient_city': 'City',
				'patient_state': 'State',
				'patient_zip': 'Zip',
				'patient_phone_number': 'Phone Number',
				'patient_date_of_birth': 'Date of Birth',
				'patient_mrn': 'MRN',
				'insurance_company': 'All Insurances',
				'primary_insurance_type': 'Primary Insurance Type',
				'insurance_phone': 'Insurance Phone #',
				'patient_insurance_id': 'Patient\'s Insurance ID',
				'total_scheduled_charges': 'Total Scheduled Charges',
				'total_coded_charges': 'Total Coded Charges',
				'billing_notes': 'Billing Notes',
				'inventory_item_name': 'Item Name',
				'inventory_item_number': 'Item Number',
				'inventory_item_description': 'Item Description',
				'inventory_qty_requested': 'Qty Requested',
				'inventory_qty_used': 'Qty Used',
				'inventory_unit_price': 'Unit Price',
				'inventory_charge_amount': 'Charge Amount',
				'inventory_manufacturer': 'Manufacturer',
				'case_canceled_within_one_day': 'Canceled within 1 day',
				'case_rescheduled': 'Rescheduled',
				'type_of_claim': 'Type of Claim(s)',
				'date_of_submission_claim': 'Date of Submission',
				'primary_insurance': 'Primary Insurance',
				'secondary_insurance': 'Secondary Insurance',
				'tertiary_insurance': 'Tertiary Insurance',
				'quaternary_insurance': 'Quaternary Insurance',
				'other_insurance': 'Other Insurance',
				'or': 'OR',
				'payments_amount': 'Payments',
				'adjustments_amount': 'Adjustments',
				'write_offs_amount': 'Write-Offs',
				'outstanding_balance': 'Balance',
				'ar_billing_status': 'Billing Status',
				'var_cost': 'Variable Cost'

			};

			var COLUMNS_GROUPS = {
				billing: [
					'insurance_company',
					'primary_insurance_type',
					'insurance_phone',
					'patient_insurance_id',
					'total_scheduled_charges',
					'total_coded_charges',
					'billing_notes',
					'type_of_claim',
					'date_of_submission_claim',
					'primary_insurance',
					'secondary_insurance',
					'tertiary_insurance',
					'quaternary_insurance',
					'other_insurance',
					'payments_amount',
					'adjustments_amount',
					'write_offs_amount',
					'outstanding_balance',
					'ar_billing_status',
					'co_pay',
					'co_insurance',
					'deductible',
					'oop'
				],
				demographic: [
					'patient_first_name',
					'patient_last_name',
					'patient_date_of_birth',
					'patient_mrn',
					'patient_country',
					'patient_city',
					'patient_state',
					'patient_street_address',
					'patient_street_address_2',
					'patient_zip',
					'patient_phone_number'
				],
				caseDetails: [
					'case_id',
					'case_duration',
					'case_description',
					'case_anesthesiologist',
					'case_anesthesia_type',
					'case_date_of_service',
					'case_start_time',
					'case_end_time',
					'case_physician',
					'case_procedure1',
					'case_procedure2',
					'case_procedure3',
					'case_procedure4',
					'coded_procedures',
					'case_special_equipment_flag',
					'case_special_equipment',
					'case_implants_flag',
					'case_implants',
					'case_actual_start_time',
					'case_actual_end_time',
					'case_actual_duration',
					'clinical_notes',
					'or',
					'var_cost'
				],
				caseDetailsForCancelledCases: [
					'case_id',
					'case_duration',
					'case_description',
					'case_anesthesiologist',
					'case_anesthesia_type',
					'case_date_of_service',
					'case_start_time',
					'case_end_time',
					'case_physician',
					'case_procedure1',
					'case_procedure2',
					'case_procedure3',
					'case_procedure4',
					'coded_procedures',
					'case_special_equipment_flag',
					'case_special_equipment',
					'case_implants_flag',
					'case_implants',
					'case_actual_start_time',
					'case_actual_end_time',
					'case_actual_duration',
					'case_canceled_within_one_day',
					'case_rescheduled',
					'clinical_notes',
					'or',
					'var_cost'
				],
				inventory: [
					'inventory_item_name',
					'inventory_item_number',
					'inventory_item_description',
					'inventory_qty_requested',
					'inventory_qty_used',
					'inventory_unit_price',
					'inventory_charge_amount',
					'inventory_manufacturer'
				]
			};

			var REPORT_TYPES = {
				BILLING: {
					id: 1,
					name: 'Billing',
					columns: [
						'case_date_of_service',
						'case_start_time',
						'case_end_time',
						'patient_last_name',
						'patient_first_name',
						'case_physician',
						'case_procedure1',
						'case_procedure2',
						'case_procedure3',
						'case_procedure4'
					]
				},
				PATIENTS: {
					id: 2,
					name: 'Patients',
					columns: [
						'case_date_of_service',
						'patient_last_name',
						'patient_first_name',
						'patient_country',
						'patient_city',
						'patient_state',
						'patient_street_address',
						'patient_street_address_2',
						'patient_zip',
						'patient_phone_number',
						'patient_date_of_birth',
						'case_id',
						'case_physician'
					]
				},
				SURGEON: {
					id: 3,
					name: 'Surgeon',
					columns: [
						'case_date_of_service',
						'case_physician',
						'patient_last_name',
						'patient_first_name',
						'case_procedure1',
						'case_procedure2',
						'case_procedure3',
						'case_procedure4'
					]
				},
				INVENTORY: {
					id: 4,
					name: 'Inventory',
					columns: [
						'patient_first_name',
						'patient_last_name',
						'patient_mrn',
						'case_id',
						'case_description',
						'case_date_of_service',
						'case_physician',
						'case_procedure1',
						'case_procedure2',
						'case_procedure3',
						'case_procedure4',
						'case_special_equipment',
						'case_special_equipment_flag',
						'case_implants',
						'case_implants_flag',
						'inventory_item_name',
						'inventory_item_number',
						'inventory_item_description',
						'inventory_qty_requested',
						'inventory_qty_used',
						'inventory_unit_price',
						'inventory_charge_amount',
						'inventory_manufacturer'
					]
				},
				CASE_BLOCK_UTILIZATION: {
					id: 5,
					name: 'Case Block Utilization',
					columns: [

					]
				},
				INFECTION: {
					id: 6,
					name: 'Infection',
					columns: [

					]
				},
				CANCELED_CASES: {
					id: 7,
					name: 'Canceled Cases',
					columns: [
						'case_date_of_service',
						'case_start_time',
						'case_end_time',
						'patient_last_name',
						'patient_first_name',
						'case_physician',
						'case_procedure1',
						'case_procedure2',
						'case_procedure3',
						'case_procedure4',
						'case_canceled_within_one_day',
						'case_rescheduled'
					]
				},
				PROCEDURES_REPORT: {
					id: 8,
					name: 'Procedures Report',
					columns: [
						'patient_mrn',
						'case_id',
						'patient_last_name',
						'patient_first_name',
						'case_physician',
						'primary_insurance',
						'case_date_of_service',
						'bill_procedure_cpt',
						'bill_procedure_description',
						'bill_procedure_charge_amount'
					]
				},
				CASES_REPORT: {
					id: 9,
					name: 'Cases Report',
					columns: [
						'case_date_of_service',
						'patient_last_name',
						'patient_first_name',
						'patient_mrn',
						'case_id',
						'case_physician',
						'insurance_type_acronym',
						'primary_insurance',
						'type_of_claim',
						'total_coded_charges',
						'payments_amount',
						'adjustments_amount',
						'deductible',
						'co_insurance',
						'co_pay',
						'oop',
						'outstanding_balance',
						'ar_billing_status',
						'case_actual_duration',
						'billing_notes'
					]
				},
				OTHER: {
					id: 10,
					name: 'Other',
					columns: [

					]
				}
			};

			var INSURANCE_TYPES = [
				{
					id: '1',
					name: 'Commercial'
				},
				{
					id : 'self_funded',
					name: 'Commercial (Self-Funded)'
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
			];

			var REPORT_TYPES_LIST = [];

			if (Permissions.hasAccess('analytics', 'view_billing')) {
				REPORT_TYPES_LIST.push(REPORT_TYPES.BILLING);
			}

			REPORT_TYPES_LIST = REPORT_TYPES_LIST.concat([
				REPORT_TYPES.PATIENTS,
				REPORT_TYPES.SURGEON,
				REPORT_TYPES.INVENTORY,
				REPORT_TYPES.CASE_BLOCK_UTILIZATION,
				REPORT_TYPES.INFECTION,
				REPORT_TYPES.CANCELED_CASES,
				REPORT_TYPES.PROCEDURES_REPORT,
				REPORT_TYPES.CASES_REPORT,
				REPORT_TYPES.OTHER
			]);

			var MANUAL_BILLING_STATUSES = {
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
			};

			var vm = this;
			vm.reportTypes = REPORT_TYPES;
			vm.reportTypesList = REPORT_TYPES_LIST;
			vm.columnGroups = COLUMNS_GROUPS;
			vm.columnLabels = COLUMNS_TO_EXPORT;
			vm.insuranceTypes = INSURANCE_TYPES;
			vm.manualBillingStatuses = MANUAL_BILLING_STATUSES;
			vm.billingConst = BillingConst;
			vm.isReportGenerating = false;
			vm.selectedParams = {
				reportType: REPORT_TYPES.OTHER.id,
				parentReportType: null,
				practiceGroups: [],
				selectedColumns: {},
				surgeons: [],
				insurances: [],
				infectionType: null
			};
			vm.errors = null;
			vm.modal = null;

			vm.reportTypeChanged = function() {
				vm.selectedParams.parentReportType = !!REPORT_TYPES[vm.selectedParams.reportType] &&
					REPORT_TYPES[vm.selectedParams.reportType].parent_id ?
					REPORT_TYPES[vm.selectedParams.reportType].parent_id : null;
				syncSelectedColumns();
			};

			vm.generateReport = function() {

				vm.errors = null;
				vm.isReportGenerating = true;

				var selectedColumns = vm.getSelectedColumnsPlain();

				var params = {};
				params.type = vm.selectedParams.reportType;
				params.columns = selectedColumns;
				if (vm.selectedParams.dateFrom) {
					params.dateFrom = moment(vm.selectedParams.dateFrom).format('YYYY-MM-DD');
				}

				if (vm.selectedParams.dateTo) {
					params.dateTo = moment(vm.selectedParams.dateTo).format('YYYY-MM-DD');
				}

				params.organization = $scope.org_id;
				params.practiceGroups = vm.selectedParams.practiceGroups;
				params.surgeons = vm.selectedParams.surgeons;
				params.insuranceTypes = vm.selectedParams.insuranceTypes;

				var insurances = [];
				if (vm.selectedParams.insurances && vm.selectedParams.insurances.length) {
					angular.forEach(vm.selectedParams.insurances, function(insurance) {
						insurances.push(insurance.id);
					});
				}
				params.insurances = insurances;

				var procedures = [];
				if (vm.selectedParams.procedures && vm.selectedParams.procedures.length) {
					angular.forEach(vm.selectedParams.procedures, function(procedure) {
						procedures.push(procedure.id);
					});
				}
				params.procedures = procedures;

				var inventoryItems = [];
				if (vm.selectedParams.inventoryItems && vm.selectedParams.inventoryItems.length) {
					angular.forEach(vm.selectedParams.inventoryItems, function(inventoryItem) {
						inventoryItems.push(inventoryItem.id);
					});
				}
				params.inventoryItems = inventoryItems;


				var manufacturers = [];
				if (vm.selectedParams.manufacturers && vm.selectedParams.manufacturers.length) {
					angular.forEach(vm.selectedParams.manufacturers, function(manufacturer) {
						manufacturers.push(manufacturer.id);
					});
				}
				params.manufacturers = manufacturers;

				var locations = [];
				if (vm.selectedParams.locations && vm.selectedParams.locations.length) {
					angular.forEach(vm.selectedParams.locations, function(location) {
						locations.push(location.id);
					});
				}
				params.locations = locations;

				params.inventoryItemTypes = vm.selectedParams.inventoryItemTypes;
				params.infectionType = vm.selectedParams.infectionType;
				params.billing_status = vm.selectedParams.billing_status;

				$http.post('/analytics/reports/ajax/generateReport', $.param({params: JSON.stringify(params)})).then(function(res) {
					vm.isReportGenerating = false;
					if (res.data.success) {
						window.location = res.data.url;
					} else {
						vm.errors = res.data.errors;
					}

				}, function() {
					vm.isReportGenerating = false;
				});
			};

			vm.generateMonthlyIPC = function() {
				vm.selectedParams.infectionType = 'ipc';
				vm.generateReport();
			};

			vm.generatePostOpInfection = function() {
				vm.selectedParams.infectionType = 'post-op';
				vm.generateReport();
			};

			vm.isDisabledInsuranceTypeItem = function (item) {
					var selfFunded = $filter('filter')(vm.selectedParams.insuranceTypes, 'self_funded');
					if(selfFunded && selfFunded.length ) {
						return !!(vm.selectedParams.insuranceTypes.length && item.id == 1);
					}

					var commercial = $filter('filter')(vm.selectedParams.insuranceTypes, '1');
					if(commercial && commercial.length ) {
						return !!(vm.selectedParams.insuranceTypes.length && item.id == 'self_funded');
					}

			};

			vm.clearAllColumns = function () {
				vm.selectedParams.reportType =  REPORT_TYPES.OTHER.id;
				vm.selectedParams.selectedColumns =  {};
			};


			vm.showSaveReportDialog = function () {
				vm.modal = $scope.dialog(View.get('analytics/reports/custom-report-modal.html'), $scope,  {
					size: 'md',
					controller: [
						'$scope', '$uibModalInstance',
						function($scope, $uibModalInstance) {
							var modalVm = this;
							modalVm.errors = [];

							modalVm.saveCustomReport = function() {
								if (modalVm.validate()) {
									$http.post('/analytics/reports/ajax/saveCustom/', $.param({
										data: {
											parent: vm.selectedParams.parentReportType ?
												vm.selectedParams.parentReportType :
												vm.selectedParams.reportType,
											columns: vm.getSelectedColumnsPlain(),
											name: modalVm.custom_report_type_name
										}
									})).then(function (res) {
										if (res.data.success) {
											let customId = 'custom_' + res.data.id;
											REPORT_TYPES[customId] = {
												id: customId,
												parent_id: vm.selectedParams.reportType,
												name: modalVm.custom_report_type_name,
												columns: vm.getSelectedColumnsPlain()
											};
											REPORT_TYPES_LIST.push(REPORT_TYPES[customId]);

											vm.selectedParams.reportType = customId;
											vm.reportTypeChanged();

											$uibModalInstance.close();
										} else {
											modalVm.errors = res.data.errors;
										}
									});
								}
							};

							modalVm.reportInputKeyDown = function(e) {
								if (modalVm.validate() && e.keyCode === 13) {
									modalVm.saveCustomReport();
								}
							};

							modalVm.prepareValue = function () {
								modalVm.custom_report_type_name = !!modalVm.custom_report_type_name ?
									modalVm.custom_report_type_name.trim() : ''
							};

							modalVm.validate = function () {
								modalVm.errors = [];
								modalVm.prepareValue();

								if (!vm.getSelectedColumnsPlain().length) {
									modalVm.errors.push(
										'Please select at least one column'
									);
								}

								if (!vm.isValidReportType(modalVm.custom_report_type_name)) {
									modalVm.errors.push(
										'Report type can contain only alphanumeric characters or spaces ' +
										'and can not be longer than 30 characters'
									);
								}

								if (!vm.isUniqueReportType(modalVm.custom_report_type_name)) {
									modalVm.errors.push(
										'Report type "' + modalVm.custom_report_type_name +
										'" is already in use - Please use a different name!'
									);
								}

								return !modalVm.errors.length;
							};

							modalVm.cancel = function() {
								$uibModalInstance.dismiss('cancel');
							};
						}],
					controllerAs: 'modalVm'
				});
			};


			vm.deleteCustomReportDlg = function(group) {
				$scope.dialog(View.get('analytics/reports/confirm-delete.html'), $scope, {windowClass: 'alert'}).result.then(function() {
					let nextType = vm.findNextReport();
					$http.post('/analytics/reports/ajax/deleteCustom/', $.param({
						id: vm.selectedParams.reportType,
					})).then(function (res) {
						delete REPORT_TYPES[vm.selectedParams.reportType];
						for (let i = 0; i < REPORT_TYPES_LIST.length; i++) {
							if (REPORT_TYPES_LIST[i].id == vm.selectedParams.reportType) {
								REPORT_TYPES_LIST.splice(i, 1);
								break;
							}
						}

						vm.selectedParams.reportType = nextType;
						vm.reportTypeChanged();
					});
				});
			};

			vm.findNextReport = function () {
				let allTypes = Object.keys(REPORT_TYPES);
				let currentTypeIndex = allTypes.indexOf(vm.selectedParams.reportType);

				return !!allTypes[currentTypeIndex + 1] ? allTypes[currentTypeIndex + 1] :
					(!!allTypes[currentTypeIndex - 1] ? allTypes[currentTypeIndex - 1] : (
						REPORT_TYPES.OTHER.id
					));
			};


			vm.isValidReportType = function (customType) {
				return customType && customType.length > 0 && customType.length <= 30 &&
					customType.match(/^[\w_\s\-]+$/gui);
			};

			vm.isUniqueReportType = function (customType) {
				var isUnique = true;
				if (customType) {
					var customTypeLower = customType.toLowerCase();
					angular.forEach(REPORT_TYPES, function(type) {
						if (type.name.toLowerCase() == customTypeLower) {
							isUnique = false;
						}
					});
				}

				return isUnique;
			};

			vm.getSelectedColumnsPlain = function () {
				var selectedColumns = [];
				angular.forEach(vm.selectedParams.selectedColumns, function(value, key) {
					if (value) {
						selectedColumns.push(key);
					}
				});

				return selectedColumns;
			};

			loadCustomReports();
			syncSelectedColumns();

			function loadCustomReports() {
				$http.get('/analytics/reports/ajax/getCustom/', {}).then(function (response) {
					if (response.data.success) {
						angular.forEach(response.data.data, function(type) {
							let customId = 'custom_' + type.id;
							type.id = customId;
							REPORT_TYPES[customId] = type;
							REPORT_TYPES_LIST.push(type);
						});
					}
				});
			}

			function syncSelectedColumns() {
				vm.selectedParams.selectedColumns = {};
				if (vm.selectedParams.reportType) {
					angular.forEach(REPORT_TYPES, function(type) {
						if (type.id == vm.selectedParams.reportType) {
							angular.forEach(type.columns, function(column) {
								vm.selectedParams.selectedColumns[column] = true;
							});
							return false;
						}
					})

				}
			}

			function getSelectedReportType() {

				var result = null;
				angular.forEach(REPORT_TYPES, function(obj) {
					if (obj.id == vm.selectedParams.reportType) {
						result = obj;
						return false;
					}
				});

				return result;
			}
		}]);
})(opakeApp, angular);
