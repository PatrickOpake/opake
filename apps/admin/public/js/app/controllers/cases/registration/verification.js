(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('VerificationCtrl', [
		'$rootScope',
		'$scope',
		'$http',
		'$timeout',
		'$window',
		'$q',
		'$filter',
		'$location',
		'Tools',
		'View',
		'Insurances',
		'CaseRegistrationConst',
		'InsurancesWidgetService',

		function ($rootScope, $scope, $http, $timeout, $window, $q, $filter, $location, Tools, View, Insurances, CaseRegistrationConst, InsurancesWidgetService) {
			var vm = this;

			vm.errors = [];

			vm.caseRegistrationId = null;
			vm.initOptions = {};

			vm.activeTab = 0;

			vm.insurances = [];
			vm.regCpts = [];

			vm.insuranceId = null;
			vm.insurance = null;
			vm.data = null;
			vm.origData = null;

			vm.editInsurance = false;

			vm.isSaving = false;


			$scope.$on('changedAdditionalCpts', function (e, data) {
				vm.regCpts = data.additional_cpts;

				if (vm.insuranceId) {
					vm.data.cpts = vm.mergeCpts(vm.data.cpts, vm.regCpts);
				}
			});

			$scope.$on('Booking.PatientChanged', function(e, patientId, patient, booking) {
				vm.init(vm.caseRegistrationId, null, null, vm.initOptions);
			});

			$scope.$on('Registration.PatientSaved', function(e, patientId, patient, registration) {
				vm.init(vm.caseRegistrationId, null, null, vm.initOptions);
			});

			$scope.$on('Case.PatientSaved', function(e, patientId, patient, caseItem) {
				vm.init(vm.caseRegistrationId, null, null, vm.initOptions);
			});

			$scope.$on('Billing.CodingSaved', function() {
				vm.init(vm.caseRegistrationId, null, null, vm.initOptions);
			});

			$scope.$on('Eligibility.Updated', function(e, EligibilityVm) {
				vm.activeTab = 2;
			});


			vm.init = function(caseRegistrationId, insurances, caseCpts, options) {
				vm.caseRegistrationId = caseRegistrationId;
				vm.insurances = insurances;
				vm.regCpts = caseCpts;
				vm.initOptions = options || {};

				var def = $q.defer();

				var afterRegLoad = function () {
					vm.fromVerificationQueue = !!vm.initOptions.isVerificationQueue;

					def.resolve();
				};

				if (insurances) {
					afterRegLoad();
				} else {
					$http.get('/cases/registrations/ajax/' + $scope.org_id + '/registration/' + vm.caseRegistrationId).then(function (result) {
						let data = result.data;
						let caseId = data.case_id;
						vm.insurances = data.insurances;

						$http.get('/cases/ajax/' + $scope.org_id + '/case/' + caseId, {}).then(function (result) {
							let data = result.data;
							vm.regCpts = data.additional_cpts;
							afterRegLoad();
						});
					});
				}

				return def.promise;
			};

			vm.save = function () {
				if (vm.data.verification_status == CaseRegistrationConst.APPOINTMENT_STATUS.COMPLETED) {
					$scope.dialog(View.get('cases/verification/confirm_save.html'), $scope, {windowClass: 'alert'}).result.then(function () {
						vm.saveVerification();
					});
				} else {
					vm.saveVerification();
				}
			};

			vm.saveVerification = function() {
				vm.isSaving = true;
				vm.errors = [];

				let params = {
					caseRegistrationId: vm.caseRegistrationId,
					caseInsuranceId: vm.insuranceId,
					verification: vm.data,
				};

				$http.post('/verification/ajax/' + $scope.org_id + '/save/', $.param({data: JSON.stringify(params)})).then(function (result) {
					if (result.data.id) {
						vm.data.id = result.data.id;
						vm.data.case_registration_id = vm.caseRegistrationId;
						vm.data.case_insurance_id = vm.insuranceId;
						vm.insurance.verification = angular.copy(vm.data);
						vm.origData = angular.copy(vm.data);
						//vm.closeInsurance();

						$rootScope.$emit('flashAlertMessage', 'Saved');
					}
					else if (result.data.errors) {
						vm.errors = result.data.errors;
						vm.activeTab = 1;
					}

					vm.isSaving = false;
				}, function(error) {
					vm.errors = [error.data.message];
					vm.activeTab = 1;
					vm.isSaving = false;
				});
			};

			vm.selectInsurance = function (insurance) {
				vm.insuranceId = insurance.id;
				vm.insurance = insurance;
				vm.data = angular.copy(vm.insurance.verification);
				vm.data.cpts = vm.mergeCpts(vm.data.cpts, vm.regCpts);
				vm.origData = angular.copy(vm.data);
			};

			vm.closeInsurance = function () {
				vm.insurance = null;
				vm.insuranceId = null;
				vm.data = null;
				vm.origData = null;
				vm.activeTab = 0;
				vm.errors = [];
			};

			vm.isDataChanged = function () {
				return !angular.equals(vm.data, vm.origData);
			};

			vm.getInsurancesForVerification = function(insurances) {
				var result = [];
				angular.forEach(insurances, function(ins) {
					if (ins.id && result.type != 6 && result.type != 8) {
						result.push(ins);
					}
				});

				return result;
			};


			vm.mergeCpts = function(cpts, regCpts) {
				var cptsByCodes = {};
				angular.forEach(cpts, function(cpt) {
					cptsByCodes[cpt.case_type.id] = cpt;
				});

				angular.forEach(regCpts, function (item) {
					if (item.id in cptsByCodes) {
						cptsByCodes[item.id].is_case_procedure = true;
					}
					else {
						cptsByCodes[item.id] = Insurances.getAdditionalCodeMaster(item, 1);
					}
				});

				var result = [];
				angular.forEach(cptsByCodes, function (item) {
					result.push(item);
				});

				return result;
			};

			vm.addAdditionalCode = function(cpt) {
				vm.data.cpts.push(cpt);
			};

			vm.deleteCpt = function(cpts_index, cpt_item) {
				$scope.dialog(View.get('cases/registrations/insurance/confirm_delete_cpt.html'), $scope, {windowClass: 'alert confirm-delete'}).result.then(function () {
					if(cpts_index > -1) {
						vm.data.cpts.splice(cpts_index, 1);
					}
				});
			};


			vm.toVerificationQueue = function () {
				var params = $location.search();
				$window.location = '/verification/' + $scope.org_id + '#?p=' + params.p + '&l=' + params.l;
			};


			vm.startEditInsurance = function () {
				vm.editInsurance = true;
			};

			function saveCurrentInsurance() {
				return InsurancesWidgetService.tryToSaveOpenedInsurance();
			}
		}]);

})(opakeApp, angular);
