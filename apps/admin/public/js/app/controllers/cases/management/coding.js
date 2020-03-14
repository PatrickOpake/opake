// Case Coding
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseCodingCrtl', [
		'$scope',
		'$http',
		'$window',
		'$filter',
		'$q',
		'View',
		'Tools',
		'CaseCoding',
		'CodingConst',
		'BeforeUnload',
		'BillingNotes',
		'PatientInsurance',
		'InsurancesWidgetService',
		function ($scope, $http, $window, $filter, $q, View, Tools, CaseCoding, CodingConst, BeforeUnload, BillingNotes, PatientInsurance, InsurancesWidgetService) {
			$scope.codingConst = CodingConst;

			var vm = this;
			var caseId,
				originalCoding;

			vm.data = {};
			vm.errors = null;
			vm.isFormContentLoaded = false;
			vm.hasSavedCoding = false;
			vm.isSaving = false;
			vm.billingNotes = BillingNotes;

			vm.init = function (caseid) {
				caseId = caseid;
				loadCoding();
			};

			vm.editCase = function () {
				$window.location = '/cases/' + $scope.org_id + '/cm/' + caseId;
			};

			vm.save = function () {
				if (!vm.isSaving) {
					if (vm.isOriginalClaimRequired() && !vm.coding.original_claim_id) {
						vm.errors = ['Original Claim# must be specified'];
						return;
					}

					vm.isSaving = true;
					vm.errors = null;
					InsurancesWidgetService.tryToSaveOpenedInsurance().then(function() {
						$http.post('/cases/ajax/coding/' + $scope.org_id + '/save/' + caseId, $.param({
							data: JSON.stringify(vm.coding)
						})).then(function(result) {
							if (!result.data.success) {
								vm.errors = result.data.errors;
								BeforeUnload.clearForms('case_coding');
								BeforeUnload.reset();
							} else {
								loadCoding(true).then(function() {
									vm.isSaving = false;
									$scope.$emit('Billing.CodingSaved');
									$scope.$broadcast('Billing.CodingSaved');
								});
							}
						}, function() {
							vm.isSaving = false
						});
					});
				}
			};

			vm.saveDuplicate = function() {
				if (!vm.isSaving && vm.isChanged()) {
					if (vm.isOriginalClaimRequired() && !vm.coding.original_claim_id) {
						vm.errors = ['Original Claim# must be specified'];
						return false;
					}

					vm.isSaving = true;
					vm.errors = null;

					$http.post('/cases/ajax/coding/' + $scope.org_id + '/saveDuplicate/' + caseId, $.param({
						data: JSON.stringify({
							bill_type: vm.coding.bill_type,
							original_claim_id: vm.coding.original_claim_id,
							reference_number: vm.coding.reference_number,
						})
					})).then(function(result) {
						if (!result.data.success) {
							vm.errors = result.data.errors;
						} else {
							vm.isSaving = false;
							originalCoding.original_claim_id = vm.coding.original_claim_id;
							originalCoding.bill_type = vm.coding.bill_type;
							originalCoding.reference_number = vm.coding.reference_number;
						}
					}, function() {
						vm.isSaving = false
					});
				}

				return true;
			};

			vm.cancel = function () {
				vm.coding = angular.copy(originalCoding);
			};

			vm.isChanged = function () {
				return !BeforeUnload.compareForms(vm.coding, originalCoding);
			};

			vm.generateUB04 = function () {
				$window.open('/cases/ajax/coding/' + $scope.org_id + '/generateUB04/' + caseId);
			};

			vm.generate1500 = function () {
				$window.open('/cases/ajax/coding/' + $scope.org_id + '/generate1500/' + caseId);
			};

			vm.billTypeChanged = function () {
				if (!vm.isOriginalClaimRequired()) {
					vm.coding.original_claim_id = null;
				}
				else if (!vm.coding.original_claim_id && originalCoding.original_claim_id) {
					vm.coding.original_claim_id = originalCoding.original_claim_id;
				}

				if (!vm.isReferenceNumberRequired()) {
					vm.coding.reference_number = null;
				}
				else if (!vm.coding.reference_number && originalCoding.reference_number) {
					vm.coding.reference_number = originalCoding.reference_number;
				}
			};

			vm.isOriginalClaimRequired = function() {
				return vm.coding.bill_type == 6;
			};

			vm.isReferenceNumberRequired = function() {
				return vm.coding.bill_type == 7;
			};

			vm.resetLabServicesOutsideAmount = function () {
				if (vm.coding) {
					vm.coding.lab_services_outside_amount = null;
				}
			};

			vm.addBill = function () {
				vm.coding.bills.push({
					quantity: 1
				});
			};

			vm.billHCPCSUpdated = function (bill) {
				if (bill.charge_master_entry && bill.charge_master_entry.id) {
					$http.get('/cases/ajax/coding/' + $scope.org_id + '/additionalHcpcsInfo/', {params: {charge_master_entry_id: bill.charge_master_entry.id, case_id: caseId}}).then(function (result) {
						var data = result.data;
						bill.charge = data.charge;
						bill.revenue_code = data.revenue_code;
						bill.modifiers = data.modifiers;
						if (data.modifiers && data.modifiers.length) {
							bill.mod = data.modifiers[0];
						}
						vm.quantityOrChargeChanged(bill);
					}, function() {
						bill.charge = '';
						bill.revenue_code = '';
						bill.amount = '';
					});
				} else {
					bill.charge = '';
					bill.revenue_code = '';
					bill.amount = '';
				}
			};

			vm.getModifiers = function (type) {
				var deferred = $q.defer();
				if (type && type.id) {
					$http.get('/cases/ajax/coding/' + $scope.org_id + '/fees/' + caseId, {params: {case_type_id: type.id}}).then(function (result) {
						deferred.resolve(result.data);
					});
				} else {
					deferred.resolve([]);
				}
				return deferred.promise;
			};

			vm.billModifierUpdated = function (bill) {

				if (bill.mod && bill.mod.charge_master_entry) {

					bill.charge_master_entry = {
						id: bill.mod.charge_master_entry.id,
						title: bill.mod.charge_master_entry.title
					};

					$http.get('/cases/ajax/coding/' + $scope.org_id + '/additionalHcpcsInfo/', {params: {
						charge_master_entry_id: bill.charge_master_entry.id,
						case_id: caseId
					}}).then(function (result) {
						var data = result.data;
						bill.charge = data.charge;
						bill.revenue_code = data.revenue_code;
						vm.quantityOrChargeChanged(bill);
					}, function() {
						bill.charge = '';
						bill.revenue_code = '';
						bill.amount = '';
					});

				}
			};

			vm.getAvailableDiagnosesRows = function () {
				var result = [];
				if (vm.coding) {
					angular.forEach(CodingConst.DIAGNOSIS_ROWS, function (item) {
						var row = vm.coding.getDiagnosis(item.id);
						if (row && row.icd) {
							result.push(item);
						}
					});
				}
				return result;
			};

			vm.getUsedValueCodesIds = function () {
				var usedValueCodesIds = [];
				angular.forEach(vm.coding.values, function (value) {
					if (value.value_code && value.value_code.id) {
						usedValueCodesIds.push(value.value_code.id);
					}
				});

				return usedValueCodesIds;
			};

			vm.getUsedOccurrenceCodesIds = function () {
				var usedOccurrenceCodesIds = [];
				angular.forEach(vm.coding.occurrences, function (occurrence) {
					if (occurrence.occurrence_code && occurrence.occurrence_code.id) {
						usedOccurrenceCodesIds.push(occurrence.occurrence_code.id);
					}
				});

				return usedOccurrenceCodesIds;
			};

			vm.getUsedIcdCodesIds = function () {
				var usedIcdCodesIds = [];
				angular.forEach(vm.coding.diagnoses, function (diagnosis) {
					if (diagnosis.icd && diagnosis.icd.id) {
						usedIcdCodesIds.push(diagnosis.icd.id);
					}
				});

				return usedIcdCodesIds;
			};

			vm.quantityOrChargeChanged = function(bill) {
				if (bill.charge === undefined || bill.charge === '' || bill.charge === null) {
					bill.amount = null;
					return;
				}
				if (bill.quantity === undefined || bill.quantity === '' || bill.quantity === null) {
					bill.amount = bill.charge;
					return;
				}
				var quantity = parseInt(bill.quantity, 10);
				var charge = parseFloat(bill.charge);
				bill.amount = (charge * quantity).toFixed(2);
			};

			vm.removeHCPCSRow = function (item) {
				vm.modalDelete = $scope.dialog(View.get('patients/confirm_delete_zero.html'), $scope, {windowClass: 'alert'});
				vm.modalDelete.result.then(function () {
					var idx = vm.coding.bills.indexOf(item);
					if (idx > -1) {
						vm.coding.bills.splice(idx, 1);
					}
				});
			};

			vm.newModifier = function (name) {
				if (name) {
					return {
						id: null,
						name: name,
						is_custom: true
					};
				}

				return undefined;
			};

			vm.viewClinicalCharts = function (patientId) {
				$scope.dialog(
					View.get('billing/modal-clinical-charts.html'),
					$scope, {
						size: 'lg',
						backdrop: 'static',
						controller: ['$scope', '$controller', '$uibModalInstance', 'Case', 'PatientChart', function ($scope, $controller, $uibModalInstance, Case, PatientChart) {

							var vm = this;

							vm.typeDocName = 'Chart';
							vm.typeDoc = 'chart';
							vm.errors = null;
							vm.isLoading = true;

							$controller('AbstractDocumentsCtrl', {vm: vm, $scope: $scope});

							$http.get('/patients/ajax/' + $scope.org_id + '/charts/' + patientId).then(function (result) {
								var cases = [];
								var casesDocsLength = 0;
								vm.docsToUpload = [];
								vm.foldersChoiceList = [];
								var generalFolder = {folder_id: 'general', text: 'General Charts'};
								vm.foldersChoiceList.push(generalFolder);

								angular.forEach(result.data.cases, function (case_item) {
									var caseObject = new Case(case_item);
									if(caseObject.report) {
										caseObject.report.name = 'Operative Report';
										caseObject.report.uploaded_date = moment(caseObject.time_start).toDate();
										caseObject.report.type = 'report';
										casesDocsLength++;
									}
									casesDocsLength += caseObject.charts.length;
									cases.push(caseObject);
									var caseFolder = {
										folder_id: caseObject.id,
										text: moment(caseObject.time_start).format('M/D/YYYY') + ' - ' +
										caseObject.first_surgeon_name + ' - ' +
										caseObject.type_name
									};
									vm.foldersChoiceList.push(caseFolder);
								});
								vm.cases = cases;

								var general_docs = [];
								angular.forEach(result.data.patientCharts, function (doc) {
									var docObject = new PatientChart(doc);
									general_docs.push(docObject);
								});
								vm.general_docs = general_docs;

								vm.fullDocLength = vm.general_docs.length + casesDocsLength;
							}).finally(function() {
								vm.isLoading = false;
							});

							vm.cancel = function() {
								$uibModalInstance.close();
							};

						}],
						controllerAs: 'docVm'
					});
			};

			vm.viewFinancialDocuments = function (patientId) {
				$scope.dialog(
					View.get('billing/modal-financial-docs.html'),
					$scope, {
						size: 'lg',
						backdrop: 'static',
						controller: ['$scope', '$controller', '$uibModalInstance', 'Case', 'PatientFinancialDocument', function ($scope, $controller, $uibModalInstance, Case, PatientFinancialDocument) {

							var vm = this;

							vm.typeDocName = 'Financial document';
							vm.typeDoc = 'financial_document';
							vm.errors = null;
							vm.isLoading = true;

							$controller('AbstractDocumentsCtrl', {vm: vm, $scope: $scope});

							$http.get('/patients/ajax/' + $scope.org_id + '/financialDocuments/' + patientId).then(function (result) {
								var cases = [];
								var casesDocsLength = 0;
								vm.docsToUpload = [];
								vm.foldersChoiceList = [];
								var generalFolder = {folder_id: 'general', text: 'General Financial Documents'};
								vm.foldersChoiceList.push(generalFolder);

								angular.forEach(result.data.cases, function (case_item) {
									var caseObject = new Case(case_item);
									casesDocsLength += caseObject.financial_documents.length;
									cases.push(caseObject);
									var caseFolder = {
										folder_id: caseObject.id,
										text: moment(caseObject.time_start).format('M/D/YYYY') + ' - ' +
										caseObject.first_surgeon_name + ' - ' +
										caseObject.type_name
									};
									vm.foldersChoiceList.push(caseFolder);
								});
								vm.cases = cases;

								var general_docs = [];
								angular.forEach(result.data.financialDocuments, function (doc) {
									var docObject = new PatientFinancialDocument(doc);
									general_docs.push(docObject);
								});
								vm.general_docs = general_docs;

								vm.fullDocLength = vm.general_docs.length + casesDocsLength;
							}).finally(function() {
								vm.isLoading = false;
							});

							vm.cancel = function() {
								$uibModalInstance.close();
							};

						}],
						controllerAs: 'docVm'
					});
			};

			vm.getSentClaims = function () {
				var deferred = $q.defer();
				var src = '/cases/ajax/coding/claim/' + $scope.org_id + '/getAllClaims/' + caseId;
				if (angular.isDefined(vm.data[src])) {
					deferred.resolve(vm.data[src]);
				}
				else {
					$http.get(src).then(function(response) {
						vm.data[src] = response.data.claims;
						deferred.resolve(response.data.claims);
					});
				}

				return deferred.promise;
			};

			$scope.$on('Billing.ClaimSent', function() {
				vm.data = {};
			});

			function loadCoding(updateRegistration) {
				var def = $q.defer();
				var insurancesDef = $q.defer();

				$http.get('/cases/ajax/coding/' + $scope.org_id + '/getCaseInsurances/' + caseId).then(function (result) {
					if (result.data.success) {
						var caseInsurances = [];
						angular.forEach(result.data.insurances, function(insurance) {
							caseInsurances.push(new PatientInsurance(insurance));
						});
						insurancesDef.resolve(caseInsurances);
					}
				});

				$http.get('/cases/ajax/coding/' + $scope.org_id + '/coding/' + caseId).then(function (result) {
					if (result.data.coding) {
						vm.coding = new CaseCoding(result.data.coding);
						if (!vm.coding.id && (result.data.case_primary_insurance_type || result.data.case_secondary_insurance_type)) {
							vm.coding.preFillOccurrences(result.data.case_primary_insurance_type, result.data.case_secondary_insurance_type);
						}

						prepareDiagnoses();
						prepareOccurrences();
						prepareValues();

						insurancesDef.promise.then(function(caseInsurances) {
							vm.coding.case_insurances = caseInsurances;
							if (updateRegistration) {
								$scope.regVm.registration.insurances = angular.copy(caseInsurances);
							}
							originalCoding = angular.copy(vm.coding);

							if (vm.coding.id) {
								vm.hasSavedCoding = true;
							}

							vm.isFormContentLoaded = true;
							BeforeUnload.reset();
							BeforeUnload.clearForms('case_coding');
							BeforeUnload.clearForms('case_verification');
							BeforeUnload.addForms(vm.coding, originalCoding, 'case_coding');
							BeforeUnload.add(function () {
								if (!BeforeUnload.compareForms(vm.coding, originalCoding)) {
									return 'Coding form has been changed. All changes will not be saved.';
								}
							});

							def.resolve();
						});
					}
				});

				return def.promise;
			}

			function prepareDiagnoses() {
				angular.forEach(CodingConst.DIAGNOSIS_ROWS, function (item) {
					if (!vm.coding.getDiagnosis(item.id)) {
						vm.coding.diagnoses.push({
							row: item.id
						});
					}
				});

				var groups = [],
					middle = Math.ceil(CodingConst.DIAGNOSIS_ROWS.length / 2);
				groups.push(CodingConst.DIAGNOSIS_ROWS.slice(0, middle));
				groups.push(CodingConst.DIAGNOSIS_ROWS.slice(middle));
				vm.diagnosisGroups = groups;
			}

			function prepareOccurrences() {
				var groups = [];
				groups.push([vm.coding.occurrences[0], vm.coding.occurrences[1]]);
				groups.push([vm.coding.occurrences[2], vm.coding.occurrences[3]]);
				groups.push([vm.coding.occurrences[4], vm.coding.occurrences[5]]);
				groups.push([vm.coding.occurrences[6], vm.coding.occurrences[7]]);
				vm.occurrencesGroups = groups;
			}

			function prepareValues() {
				var groups = [];
				groups.push([vm.coding.values[0], vm.coding.values[1], vm.coding.values[2]]);
				groups.push([vm.coding.values[3], vm.coding.values[4], vm.coding.values[5]]);
				groups.push([vm.coding.values[6], vm.coding.values[7], vm.coding.values[8]]);
				groups.push([vm.coding.values[9], vm.coding.values[10], vm.coding.values[11]]);
				vm.valuesGroups = groups;
			}

			vm.dndDragStart = function (event) {
				if (event.dataTransfer.setDragImage) {
					var img = new Image();
					img.src = '/i/dnd-placeholder.png';
					event.dataTransfer.setDragImage(img, 0, 0);
				}
			};


		}]);
})(opakeApp, angular);
