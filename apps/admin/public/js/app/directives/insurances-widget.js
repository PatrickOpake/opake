(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('insurancesWidget', [
		'$rootScope',
		'$filter',
		'$interval',
		'View',
		function ($rootScope, $filter, $interval, View) {
			return {
				restrict: "E",
				replace: true,
				scope: {
					patientId: '=',
					isBookingPatient: '=',
					patientForm: '=',
					modelInsurances: '=insurances',
					isOnlyView: '=?'
				},
				controller: [
					'$scope',
					'$http',
					'$q',
					'$timeout',
					'View',
					'CaseRegistrationConst',
					'PatientConst',
					'Insurances',
					'Source',
					'PatientInsurance',
					'InsurancesWidgetService',

					function ($scope, $http, $q, $timeout, View, CaseRegistrationConst, PatientConst, Insurances, Source, PatientInsurance, InsurancesWidgetService) {

						$scope.view = View;
						$scope.caseRegistrationConst = CaseRegistrationConst;
						$scope.insurances = Insurances;
						$scope.patientConst = PatientConst;
						$scope.source = Source;

						var emptyPatientData = {
							id: null,
							title: 'Create New Insurance',
							is_empty_insurance: true,
							order: null,
							data: {
								insurance_id: null,
								insurance_company_name: null,
								last_name: null,
								first_name: null,
								middle_name: null,
								suffix: null,
								dob: null,
								gender: null,
								phone: null,
								address: null,
								apt_number: null,
								country_id: null,
								state_id: null,
								custom_state: null,
								city_id: null,
								custom_city: null,
								zip_code: null,
								relationship_to_insured: null,
								type: null,
								policy_number: null,
								group_number: null,
								order: null,
								provider_phone: null,
								insurance_verified: 0,
								is_pre_authorization_completed: 0,
								address_insurance: null,
								insurance_state_id: null,
								insurance_city_id: null,
								insurance_zip_code: null,
								is_self_funded: false
							}
						};

						var originalModelInsurances = [];
						var originalBaseInsurances = [];

						var insuranceServiceFields = [
							'is_base_insurance',
							'is_model_insurance',
							'is_empty_insurance'
						];

						var vm = this;

						vm.order = {
							PRIMARY: 1,
							SECONDARY: 2,
							TERTIARY: 3,
							QUATERNARY: 4,
							OTHER: 5
						};

						vm.insurances = [];
						vm.patientInsurances = [];

						vm.isShowInsuranceSelect = false;
						vm.isSourceChoiceEnabled = false;
						vm.currentEditInsurance = null;
						vm.currentEditErrors = null;

						InsurancesWidgetService.init(vm);

						vm.init = function() {
							if (!$scope.patientForm) {
								var patientId = $scope.patientId;
								var isBookingPatient = $scope.isBookingPatient;
								if (patientId) {

									var insurancesUrl;
									if (isBookingPatient) {
										insurancesUrl = '/booking/ajax/' + $rootScope.org_id + '/patientInsurances/' + patientId;
									} else {
										insurancesUrl = '/patients/ajax/' + $rootScope.org_id + '/patientInsurances/' + patientId;
									}

									$http.get(insurancesUrl).then(function(res) {
										if (res.data.success) {
											vm.patientInsurances = [];
											vm.insurances = [];
											angular.forEach(res.data.insurances, function(insurance) {
												insurance.is_base_insurance = true;
												insurance.order = null;
												insurance = new PatientInsurance(insurance);
												vm.patientInsurances.push(insurance);
												vm.insurances.push(insurance);
											});
											var usedModelInsuranceIds = [];
											angular.forEach($scope.modelInsurances, function(modelInsurance) {
												modelInsurance.is_model_insurance = true;
												if (replacePatientInsurance(modelInsurance)) {
													usedModelInsuranceIds.push(modelInsurance.id);
												}
											});
											angular.forEach($scope.modelInsurances, function(modelInsurance) {
												if (usedModelInsuranceIds.indexOf(modelInsurance.id) === -1) {
													vm.insurances.push(modelInsurance);
												}
											});
										}
									});
								}

								vm.isSourceChoiceEnabled = true;
							} else {
								vm.insurances = $scope.modelInsurances.slice();
								angular.forEach(vm.insurances, function(insurance) {
									insurance.is_model_insurance = true;
								});
							}

							originalModelInsurances = angular.copy($scope.modelInsurances);
							originalBaseInsurances = angular.copy(vm.patientInsurances);

						};

						vm.collapseCurrentEditInsurance = function() {
							setCurrentEditInsurance(null);
						};

						vm.toggleSelectInsurance = function(insurance) {
							var isInsuranceWithoutOrder = false;
							if (insurance.order) {
								insurance.order = null;
							} else {
								isInsuranceWithoutOrder = true;
							}

							reorderInsurances();

							if (isInsuranceWithoutOrder) {
								insurance.order = getNextInsuranceOrder();
							}

							if (!insurance.is_model_insurance) {
								insurance.is_base_insurance = false;
								insurance.is_model_insurance = true;
								insurance.selected_insurance_id = insurance.id;
								insurance.id = null;
								insurance.data.id = null;
							}

							updateModelInsurances();
						};

						vm.editInsurance = function(item, $event) {
							$event.stopPropagation();
							setCurrentEditInsurance(item);
						};

						vm.deleteInsurance = function(item, $event) {
							$event.stopPropagation();

							$rootScope.dialog(View.get('patients/insurances/confirm_delete.html'), $scope, {
								windowClass: 'alert'
							}).result.then(function() {
								var insurance = item;
								if (!insurance.is_model_insurance) {
									insurance.is_base_insurance = false;
									insurance.is_model_insurance = true;
									insurance.selected_insurance_id = insurance.id;
									insurance.id = null;
									insurance.data.id = null;
								}

								insurance.is_deleted = true;
								updateModelInsurances();
							});
						};

						vm.createNewInsurance = function() {
							setCurrentEditInsurance(new PatientInsurance(angular.copy(emptyPatientData)));
						};

						vm.saveCurrentEditInsurance = function() {
							vm.currentEditInsurance = $scope.item;
							var insurance = vm.currentEditInsurance;

							var def = $q.defer();

							$http.post('/patients/ajax/' + $rootScope.org_id + '/validateInsurance/', $.param({
								data: JSON.stringify(insurance)
							})).then(function(res) {
								if (res.data.success) {
									if (!insurance.type) {
										vm.currentEditErrors = ['Insurance type is required'];
										return;
									}

									if (insurance.is_empty_insurance) {
										insurance.is_empty_insurance = false;
										insurance.is_model_insurance = true;
										vm.insurances.push(vm.currentEditInsurance);
									} else {
										if (!insurance.is_model_insurance) {
											insurance.is_base_insurance = false;
											insurance.is_model_insurance = true;
											insurance.selected_insurance_id = insurance.id;
											insurance.id = null;
											insurance.data.id = null;
										}
									}

									insurance.title = insurance.getTitle();
									insurance.order = (insurance.order != null) ? parseInt(insurance.order) : null;
									reorderOtherInsurances(insurance);
									setCurrentEditInsurance(null);
									updateModelInsurances();
									def.resolve();
								} else {
									vm.currentEditErrors = res.data.errors;
									def.reject();
								}
							});

							return def.promise;
						};

						vm.getDataTemplateSrc = function(item, isEdit) {

							var templateName = (isEdit) ? 'edit.html' : 'view.html';

							if (item.type == PatientConst.INSURANCE_TYPES_ID.AUTO_ACCIDENT) {
								return View.get('patients/insurances/auto-accident/' + templateName);
							}

							if (item.type == PatientConst.INSURANCE_TYPES_ID.WORKERS_COMP) {
								return View.get('patients/insurances/workers-company/' + templateName)
							}

							if (item.type == PatientConst.INSURANCE_TYPES_ID.LOP || item.type == PatientConst.INSURANCE_TYPES_ID.SELF_PAY) {
								return View.get('patients/insurances/description/' + templateName)
							}

							return View.get('patients/insurances/regular/' + templateName);
						};

						vm.fillInsurancePayorData = function(insuranceItem) {
							insuranceItem.fillPayorInfo();
						};

						vm.fillInsuranceAddressFromSelected = function(item) {
							item.fillAddressFromSelected();
						};

						vm.newInsuranceAddress = function(item, query) {
							item.data.provider_phone = '';
							item.data.insurance_state = null;
							item.data.insurance_city = null;
							item.data.insurance_zip_code = '';

							return {
								id: null,
								address: query,
								is_new: true
							};
						};


						vm.getInsuranceOrderTitle = function(insurance) {
							var title = PatientConst.INSURANCE_PRIMARY[insurance.order];
							if (title) {
								return title + ' Insurance';
							}

							return '';
						};

						vm.resetInsuranceData = function(insurance) {
							insurance.data = angular.copy(emptyPatientData.data);

							if (insurance.type == PatientConst.INSURANCE_TYPES_ID.SELF_PAY ||
								insurance.type == PatientConst.INSURANCE_TYPES_ID.LOP) {
								insurance.data.description = PatientConst.INSURANCE_TYPES[insurance.type];
							}

							insurance.typeChanged();
						};

						vm.isCurrentEditInsuranceChanged = function() {
							if (!vm.currentEditInsurance) {
								return false;
							}

							var insurance = vm.currentEditInsurance;

							if (insurance.is_empty_insurance) {
								return true;
							}

							insurance = angular.copy(insurance);
							var original = null;

							if (insurance.is_base_insurance) {
								original = null;
								angular.forEach(originalBaseInsurances, function(baseInsurance) {
									if (baseInsurance.id == insurance.id) {
										original = baseInsurance;
										return false;
									}
								});

								if (!insurance) {
									return true;
								}

								clearServiceFields(original);
								clearServiceFields(insurance);

								return !angular.equals(original, insurance);
							}

							if (insurance.is_model_insurance) {
								original = null;
								angular.forEach(originalModelInsurances, function(modelInsurance) {
									if (modelInsurance.id == insurance.id) {
										original = modelInsurance;
										return false;
									}
								});

								if (!insurance) {
									return true;
								}

								clearServiceFields(original);
								clearServiceFields(insurance);

								return !angular.equals(original, insurance);
							}
						};

						vm.isModelInsurancesChanged = function() {
							var copy = angular.copy($scope.modelInsurances);
							angular.forEach(originalModelInsurances, clearServiceFields);
							angular.forEach(copy, clearServiceFields);

							return !angular.equals(originalModelInsurances, copy);
						};

						vm.haveUnsavedChanges = function() {
							return (isCurrentEditInsuranceChanged() || haveModelInsurancesChanged());
						};

						vm.isShowErrors = function() {
							if (angular.isArray(vm.currentEditErrors)) {
								return vm.currentEditErrors.length;
							}

							return !!vm.currentEditErrors;
						};

						vm.drawError = function(error) {
							if (angular.isArray(error)) {
								return error[0];
							}

							return error;
						};

						$scope.$on('Booking.PatientChanged', function(e, patientId, patient, booking) {
							vm.init();
						});

						$scope.$on('Registration.PatientSaved', function(e, patientId, patient, registration) {
							vm.init();
						});

						$scope.$on('Case.PatientSaved', function(e, patientId, patient, caseItem) {
							vm.init();
						});

						$scope.$on('Billing.CodingSaved', function() {
							vm.init();
						});

						$scope.$on('Booking.PatientDeselect', function(e, patientId, patient, booking) {
							vm.insurances = [];
							vm.modelInsurances = booking.insurances;
							vm.patientInsurances = [];
							originalBaseInsurances = [];
							originalModelInsurances = [];
						});

						function getNextInsuranceOrder() {
							var lastOrder = 0;
							angular.forEach(vm.insurances, function(insurance) {
								if (insurance.order && insurance.order > lastOrder) {
									lastOrder = insurance.order;
								}
							});

							if (lastOrder < vm.order.OTHER) {
								lastOrder += 1;
							}

							return lastOrder;
						}

						function reorderInsurances() {
							var ordered = [];
							angular.forEach(vm.insurances, function(insurance) {
								if (insurance.order) {
									ordered.push(insurance);
								}
							});

							ordered.sort(function(a, b) {
								if (a.order > b.order) {
									return 1;
								}
								if (a.order < b.order) {
									return -1;
								}
								return 0;
							});

							angular.forEach(ordered, function(insurance, index) {
								var order = index + 1;
								if (order > vm.order.OTHER) {
									order = vm.order.OTHER;
								}
								insurance.order = order;
							});
						}

						function reorderOtherInsurances(targetInsurance) {
							if (targetInsurance.order) {
								angular.forEach(vm.insurances, function(insurance) {
									if (insurance != targetInsurance && insurance.order == targetInsurance.order) {
										if (insurance.order != vm.order.OTHER) {
											insurance.order = null;
										}
									}
								});
							}
						}

						function setCurrentEditInsurance(insurance) {
							vm.currentEditInsurance = insurance;
							$scope.item = vm.currentEditInsurance;
							vm.currentEditErrors = [];
						}

						function replacePatientInsurance(modelInsurance) {
							var insuranceToReplaceIndex = null;
							var patientInsuranceId = modelInsurance.selected_insurance_id;

							angular.forEach(vm.insurances, function(insurance, index) {
								if (insurance.is_base_insurance && insurance.id == patientInsuranceId) {
									insuranceToReplaceIndex = index;
								}
							});

							if (insuranceToReplaceIndex !== null) {
								vm.insurances[insuranceToReplaceIndex] = modelInsurance;
								return true;
							}

							return false;
						}

						function updateModelInsurances() {
							$scope.modelInsurances.length = 0;
							angular.forEach(vm.insurances, function(insurance) {
								if (insurance.is_model_insurance) {
									$scope.modelInsurances.push(insurance);
								}
							});
						}

						function clearServiceFields(insurance) {
							angular.forEach(insuranceServiceFields, function(name) {
								if (name in insurance) {
									delete insurance[name];
								}
							});
						}
					}
				],
				controllerAs: 'patientInsurancesVm',
				templateUrl: function () {
					return View.get('widgets/insurances.html');
				},
				link: function (scope, elem, attrs, ctrl) {

				}
			};
	}]);

})(opakeApp, angular);
