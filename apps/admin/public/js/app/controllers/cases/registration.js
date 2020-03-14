// Case Registration view/edit
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseRegistrationCtrl', [
		'$scope',
		'$rootScope',
		'$http',
		'$window',
		'$filter',
		'$location',
		'$q',
		'View',
		'Case',
		'CaseNotes',
		'CaseRegistration',
		'CaseRegistrationConst',
		'CMConst',
		'PatientConst',
		'InsuranceConst',
		'EligibleCoverageConst',
		'Cases',
		'Insurances',
		'FormDocuments',
		'Permissions',
		'BeforeUnload',

		function ($scope, $rootScope, $http, $window, $filter, $location, $q, View, Case, CaseNotes, CaseRegistration, CaseRegistrationConst, CMConst, PatientConst, InsuranceConst, EligibleCoverageConst, Cases, Insurances, FormDocuments, Permissions, BeforeUnload) {
			$scope.cases = Cases;
			$scope.insurances = Insurances;
			$scope.caseRegistrationConst = CaseRegistrationConst;
			$scope.patientConst = PatientConst;
			$scope.insuranceConst = InsuranceConst;
			$scope.eligibleCoverageConst = EligibleCoverageConst;
			$scope.formDocuments = FormDocuments;
			$scope.caseNotes = CaseNotes;

			var disableSaving = false;
			var vm = this;
			$scope.ctrl = vm;

			vm.action = 'view';
			vm.tabActivity = [
				{
					tabKey: 'patient',
					active: true
				},
				{
					tabKey: 'insurance',
					active: false
				},
				{
					tabKey: 'eligibility',
					active: false
				},
				{
					tabKey: 'forms',
					active: false
				}
			];

			vm.insuranceTitles = PatientConst.INSURANCE_TITLES;

			vm.registration = null;
			vm.validation = {is_patient_details_valid: false, is_insurance_valid: false, is_forms_valid: false};
			vm.documentUploadUrl = '';
			vm.isEditDisabled = false;
			vm.isFormEditDisabled = false;
			vm.error_model_insurance = 'cases_registration_insurance';
			vm.selectedInsuranceId = null;
			vm.isVerificationFormContentLoaded = false;

			$scope.$on('RegistrationFormDocumentsUpdated', function () {
				vm.save();
			});

			vm.init = function (id, regObject, caseObject) {
				var def = $q.defer();

				var afterRegLoad = function () {
					if(!vm.registration.insurances.length) {
						vm.registration.insurances = [];
					}

					vm.validation.is_forms_valid = vm.registration.is_forms_valid;

					vm.isEditDisabled = !Permissions.hasAccess('cases', 'edit', vm.registration);
					vm.documentUploadUrl = '/cases/registrations/ajax/' + $scope.org_id + '/upload/' + vm.registration.case_id;

					vm.model = vm.patient = vm.registration;

					//vm.validate();

					if (vm.registration.insurances.length) {
						vm.selectedInsuranceId = vm.registration.insurances[0].id;
					}

					vm.original_patient = angular.copy(vm.patient);
					// BeforeUnload.addForms(vm.patient, vm.original_patient, 'case_verification');
					vm.isVerificationFormContentLoaded = true;

					def.resolve();
				};

				if (caseObject) {
					vm.case = new Case(caseObject);
				}

				if (regObject) {
					vm.registration = new CaseRegistration(regObject);
					afterRegLoad();
				} else {
					if (id) {
						$http.get('/cases/registrations/ajax/' + $scope.org_id + '/registration/' + id).then(function (result) {
							var data = result.data;
							vm.registration = new CaseRegistration(data);
							afterRegLoad();
						});

					} else {
						def.resolve();
					}
				}

				return def.promise;
			};

			vm.initFromCase = function (reg, caseItem) {
				vm.init(reg.id, reg, caseItem).then(function() {
					if (vm.case.isAppointmentCanceled()) {
						vm.isEditDisabled = true;
					}
				});
			};

			vm.initSubMenu = function () {
				$scope.$watch('topMenuActive', function (newVal, oldVal) {
					if (!newVal || newVal === 'intake' || newVal === 'registration') {
						let menu = angular.copy(CMConst.PHASES.intake);
						if (!Permissions.hasAccess('verifications', 'view')) {
							delete menu['verification'];
						}

						$rootScope.subTopMenu = menu;
						$rootScope.subTopMenuActive = 'case_details';
					} else if (angular.equals($rootScope.subTopMenu, CMConst.PHASES.intake)) {
						$rootScope.subTopMenu = {};
					}
				});
			};

			vm.initBeforeUnload = function () {
				if(vm.action === 'edit') {
					BeforeUnload.addForms(vm.registration, vm.registration_master, 'registration');
				}
			};

			vm.changeLocation = function() {
				var params = $location.search();
				var isTabExist = false;
				angular.forEach(vm.tabActivity, function(tab, key) {
					if(params.tab === tab.tabKey) {
						isTabExist = true;
					}
				});
				if(isTabExist && angular.isDefined(params.tab) && params.tab) {
					angular.forEach(vm.tabActivity, function(tab, key) {
						vm.tabActivity[key].active = false;
						if (params.tab == tab.tabKey) {
							vm.tabActivity[key].active = true;
						}
					});
				}
			};
			vm.changeLocation();

			vm.edit = function() {
				vm.registration_master = angular.copy(vm.registration);
				vm.action = 'edit';
				vm.initBeforeUnload();
			};

			vm.validSections = function(type) {
				switch (type) {
					case 'patient_detail':
						vm.validation.is_patient_details_valid = true;
					break;
					case 'insurance':
						vm.validation.is_insurance_valid = true;
					break;
					case 'forms':
						vm.validation.is_forms_valid = true;
					break;
				}
			};

			vm.save = function (type, callback) {
				var def = $q.defer();
				if (!disableSaving) {
					disableSaving = true;
					Insurances.checkRelationship(vm.registration);
					$http.post('/cases/registrations/ajax/' + $scope.org_id + '/save/', $.param({data: JSON.stringify(vm.registration), type: type})).then(function (result) {
						var useDef = false;
						if (result.data.id) {
							vm.validSections(type);
							useDef = true;
							BeforeUnload.clearForms('registration');
							vm.init(result.data.id).then(function() {
								$scope.$broadcast('Registration.PatientSaved', vm.registration.patient.id, vm.registration.patient, vm.registration);
								vm.action = 'view';
								disableSaving = false;
								vm.original_patient = angular.copy(vm.patient);

								if (callback) {
									callback();
								}
							});
						}
						if (!useDef) {
							disableSaving = false;
						}
						def.resolve();
					});
				} else {
					def.resolve();
				}

				return def.promise;
			};

			vm.cancelEdit = function() {
				vm.registration = vm.patient = vm.registration_master;
				vm.action = 'view';
			};

			vm.cancel = function() {
				$window.location = '/cases/registrations/' + $scope.org_id;
			};

			vm.getView = function () {
				var view = 'cases/registrations/view.html';
				return View.get(view);
			};

			vm.getCaseView = function(action) {
				if(action === 'view') {
					return View.get('cases/registrations/case/'+action+'.html');
				} else {
					return View.get('cases/'+action+'.html');
				}
			};

			vm.completeRegistration = function() {
				$http.get('/cases/registrations/ajax/' + $scope.org_id + '/completeRegistration/' + vm.registration.id).then( function() {
					$window.location = '/cases/registrations/' + $scope.org_id;
				});
			};

			vm.reopenRegistration = function() {
				$http.get('/cases/registrations/ajax/' + $scope.org_id + '/reopenRegistration/' + vm.registration.id).then( function(result) {
					vm.registration.status = result.data;
				});
			};

			vm.validate = function() {
				$http.post('/cases/registrations/ajax/' + $scope.org_id + '/validate/', $.param({data: JSON.stringify(vm.registration)})).then(function (result) {
					vm.errors =  angular.fromJson(result.data.errors);
					vm.validation.is_patient_details_valid = (vm.errors.cases_registration ? false : true);
					vm.validation.is_insurance_valid = (vm.errors.cases_registration_insurance ? false : true);
				});
			};

			vm.addInsurance = function(index) {

			};

			vm.setStatusUploaded = function(doc) {
				if (!doc.status) {
					doc.status = 1;
					$http.post('/cases/registrations/ajax/' + $scope.org_id + '/changeStatus/' + vm.registration.case_id, $.param({data: JSON.stringify(doc)})).then(function () {
						vm.init(vm.registration.id);
					});
				}
			};

			vm.deselectTab = function (event, index, tabName) {
				var oldActiveTabIndex = angular.copy(vm.activeTab);
				var registration = vm.registration;
				var old_registration = vm.registration_master;
				var warnMsg = 'Are you sure you want to continue without saving your changes?';

				var changeTabFunc = function () {
					vm.init(vm.registration.id).then(function () {
						if (Permissions.hasAccess('registration', 'edit')) {
							if (
								((tabName == 'patient') && !vm.validation.is_patient_details_valid)
								|| ((tabName == 'insurance') && !vm.validation.is_insurance_valid)
								|| (tabName == 'eligibility')
							) {
								vm.edit();
							}
						}
						vm.initBeforeUnload();

					});
				};

				if(	(oldActiveTabIndex != index)
					&& (vm.tabActivity[oldActiveTabIndex].tabKey == 'patient' || vm.tabActivity[oldActiveTabIndex].tabKey == 'insurance' || vm.tabActivity[oldActiveTabIndex].tabKey == 'eligibility')
					&& (vm.action == 'edit' && !angular.equals(registration, old_registration))) {
					if(BeforeUnload.confirm(warnMsg)) {
						changeTabFunc();
					} else {
						event.preventDefault();
					}
				} else {
					changeTabFunc();
				}
			};

			vm.getPatientDetailsPrintUrl = function (caseId) {
				return '/cases/ajax/' + $scope.org_id + '/exportCase/' + caseId + '?to_download=false&print_part=patient_details';
			};

			vm.getInsurancesPrintUrl = function (caseId) {
				return '/cases/ajax/' + $scope.org_id + '/exportCase/' + caseId + '?to_download=false&print_part=insurances';
			};

			// Verification calculation

			vm.updateCoInsurance = function () {
				vm.patient.co_insurance = (100 - parseFloat(vm.patient.patients_responsibility || 0)).toFixed(2);
			};

			vm.updatePatientsResponsibility = function () {
				vm.patient.patients_responsibility = (100 - parseFloat(vm.patient.co_insurance || 0)).toFixed(2);
			};

		}]);

})(opakeApp, angular);

