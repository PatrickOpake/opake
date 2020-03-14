// Patient view/edit
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CreatePatientCtrl', [

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


			vm.patient = null;
			vm.form = false;

			vm.saveBlockingErrors = null;

			vm.isNewPatient = true;

			vm.init = function () {
				var def = $q.defer();

				$http.get('/patients/ajax/' + $scope.org_id + '/generateMrn').then(function (result) {
					var patient = new Patient({
						mrn: result.data.mrn,
						mrn_year: result.data.mrn_year
					});
					$http.get('/organizations/ajax/' + $scope.org_id + '/organizationProfile/').then(function (result) {
						patient.display_point_of_contact = result.data.display_point_of_contact;
					});
					patient.insurances = [];
					vm.model = vm.patient = patient;
					vm.form = true;
					vm.validate();
					vm.hangWatchToPointOfSMS();
					def.resolve();
				});

				return def.promise;
			};

			vm.cancel = function () {
				$window.history.back();
			};

			vm.canCreate = function () {
				return !vm.saveButtonDisabled && (vm.patient.first_name && vm.patient.last_name && vm.patient.dob);
			};

			vm.create = function () {
				$http.post('/patients/ajax/' + $scope.org_id + '/hasSamePatientExists/', $.param({data: JSON.stringify(vm.patient)})).then(function (result) {
					if (result.data.patient_exists) {
						$scope.dialog(View.get('patients/create_duplicate_patient.html'), $scope, {windowClass: 'alert', size: 'md'}).result.then(function () {
							vm.save();
						}, function () {
							BeforeUnload.reset(true);
							window.location = '/patients/' + $scope.org_id;
						});
					} else {
						vm.save();
					}
				});
			};

			vm.save = function() {
				vm.saveBlockingErrors = null;
				if (!vm.saveButtonDisabled) {
					vm.saveButtonDisabled = true;

					saveCurrentInsurance().then(function () {
						Insurances.checkRelationship(vm.patient);
						$http.post('/patients/ajax/' + $scope.org_id + '/save/', $.param({
							data: JSON.stringify(vm.patient)
						})).then(function (result) {
							var useDef = false;
							if (result.data.id) {
								BeforeUnload.reset(true);
								$window.location = '/patients/' + $scope.org_id + '/view/' + result.data.id;
								return;
							} else if (result.data.error) {
								vm.saveBlockingErrors = result.data.error.split(';');
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

			vm.validate = function() {
				return $http.post('/patients/ajax/' + $scope.org_id + '/validate/', $.param({data: JSON.stringify(vm.patient)})).then(function (result) {
					vm.errors =  angular.fromJson(result.data.errors);
				});
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
