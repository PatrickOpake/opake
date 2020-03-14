// Patient view/edit
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PatientCrtl', [

		'$scope',
		'$http',
		'$window',
		'$q',
		'View',
		'Patient',
		'PatientConst',
		'CaseRegistrationConst',
		'Insurances',
		'InsuranceConst',
		'EligibleCoverageConst',
		'BeforeUnload',
		'InsurancesWidgetService',

		function ($scope, $http, $window, $q, View, Patient, PatientConst, CaseRegistrationConst,
		          Insurances, InsuranceConst, EligibleCoverageConst, BeforeUnload, InsurancesWidgetService) {

			$scope.insurances = Insurances;
			$scope.patientConst = PatientConst;
			$scope.caseRegistrationConst = CaseRegistrationConst;
			$scope.insuranceConst = InsuranceConst;
			$scope.eligibleCoverageConst = EligibleCoverageConst;

			var vm = this;
			$scope.ctrl = vm;

			vm.saveButtonDisabled = false;
			vm.insuranceTitles = PatientConst.INSURANCE_TITLES;
			vm.error_model_insurance = 'patient_insurance';

			vm.tabs = [
				{tabKey: 'encounters'},
				{tabKey: 'patientDetails'},
				{tabKey: 'insurance'},
				{tabKey: 'eligibility'},
				{tabKey: 'charts'},
				{tabKey: 'operative_report'},
				{tabKey: 'notes'},
				{tabKey: 'billing-notes'}
			];

			vm.patient = null;
			vm.form = false;

			vm.insuranceErrors = null;
			vm.patientDetailErrors = null;
			vm.isEligibilityChecking = false;
			vm.eligibleErrors = {};

			vm.isNewPatient = false;

			vm.init = function (id) {
				var def = $q.defer();

				$http.get('/patients/ajax/' + $scope.org_id + '/patient/' + id).then(function (result) {
					vm.model = vm.patient = new Patient(result.data);
					vm.validate();
					vm.isNewPatient = false;
					vm.hangWatchToPointOfSMS();
					def.resolve();
				});

				return def.promise;
			};

			vm.cancel = function () {
				vm.insuranceErrors = null;
				vm.patientDetailErrors = null;
				if (vm.patient_master) {
					vm.patient = vm.patient_master;
					vm.form = false;
					vm.validate();
				} else {
					$window.history.back();
				}
			};

			vm.edit = function () {
				vm.patient_master = angular.copy(vm.patient);
				vm.form = true;
			};

			vm.savePatientDetails = function(exit) {
				vm.patientDetailErrors = null;
				if (!vm.saveButtonDisabled) {
					vm.saveButtonDisabled = true;

					$http.post('/patients/ajax/' + $scope.org_id + '/save/', $.param({
						data: JSON.stringify(vm.patient),
						form: 'patient_details'
					})).then(function (result) {
						var useDef = false;
						if (result.data.id) {
							BeforeUnload.reset(true);
							useDef = true;
							vm.init(result.data.id).then(function () {
								$scope.$broadcast('Patient.PatientSaved', vm.patient.id, vm.patient);
								vm.saveButtonDisabled = false;
								vm.form = false;
							});
						} else if (result.data.error) {
							vm.patientDetailErrors = result.data.error.split(';');
						}
						vm.validate();
						if (!useDef) {
							vm.saveButtonDisabled = false;
						}
					});
				}
			};

			vm.saveInsurances = function (exit) {
				vm.insuranceErrors = null;
				if (!vm.saveButtonDisabled) {
					vm.saveButtonDisabled = true;

					saveCurrentInsurance().then(function () {
						Insurances.checkRelationship(vm.patient);
						$http.post('/patients/ajax/' + $scope.org_id + '/save/', $.param({
							data: JSON.stringify(vm.patient),
							form: 'insurances'
						})).then(function (result) {
							var useDef = false;
							if (result.data.id) {
								BeforeUnload.reset(true);
								useDef = true;
								vm.init(result.data.id).then(function () {
									$scope.$broadcast('Patient.PatientSaved', vm.patient.id, vm.patient);
									vm.saveButtonDisabled = false;
									vm.form = false;
								});
							} else if (result.data.error) {
								vm.insuranceErrors = result.data.error.split(';');
							}
							vm.validate();
							if (!useDef) {
								vm.saveButtonDisabled = false;
							}
						});
					}, function(){
						vm.saveButtonDisabled = false;
					});
				}
			};

			vm.getView = function () {
				return View.get('patients/view.html');
			};

			vm.deselectTab = function (event, index, tabName) {
				var oldActiveTabIndex = angular.copy(vm.activeTab);
				var patient = vm.patient;
				var old_patient = vm.patient_master;
				var errors = vm.errors.patient;
				var warnMsg = 'Are you sure you want to continue without saving your changes?';

				if (vm.form && vm.tabs[oldActiveTabIndex].tabKey == 'insurance') {
					errors = vm.errors.patient_insurance;
				}

				var changeTabFunc = function () {
					angular.forEach(vm.tabs, function(v, k) {
						if (v.tabKey == tabName) {
							vm.activeTab = k;
						}
					});
					//if (errors) {
					//	vm.edit();
					//}
				};

				if ((oldActiveTabIndex != index)
					&& ((vm.tabs[oldActiveTabIndex].tabKey == 'patientDetails' || vm.tabs[oldActiveTabIndex].tabKey == 'insurance') && (vm.form && !angular.equals(patient, old_patient)))
					|| (vm.tabs[oldActiveTabIndex].tabKey == 'insurance' && InsurancesWidgetService.isCurrentEditInsuranceChanged())) {
						if (BeforeUnload.confirm(warnMsg)) {
							changeTabFunc();
						} else {
							event.preventDefault();
						}
				} else {
					changeTabFunc();
				}
			};

			vm.validate = function() {
				return $http.post('/patients/ajax/' + $scope.org_id + '/validate/', $.param({data: JSON.stringify(vm.patient)})).then(function (result) {
					vm.errors =  angular.fromJson(result.data.errors);
				});
			};

			vm.addInsurance = function(index) {

			};

			vm.showCheckEligibility = function(item) {
				return false;
			};

			vm.showSaveButtonsForExistedPatient = function() {
				return (!$scope.patientInsurancesVm || !$scope.patientInsurancesVm.currentEditInsurance);
			};

			vm.hangWatchToPointOfSMS = function () {
				if(!vm.patient.point_of_contact_phone) {
					$scope.$watch(function () {
						if(vm.patient) {
							return vm.patient.point_of_contact_phone;
						}
					}, function(val) {
						if(val) {
							if(val.length === 10 && !vm.patient.point_of_contact_phone_type) {
								vm.patient.point_of_contact_phone_type = PatientConst.TYPE_PHONE_NAMES.CELL + '';
							}
						}
					}, true);
				}
			};

			function saveCurrentInsurance() {
				return InsurancesWidgetService.tryToSaveOpenedInsurance();
			}

		}]);

})(opakeApp, angular);
