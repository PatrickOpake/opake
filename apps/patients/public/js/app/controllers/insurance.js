(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('InsuranceCtrl', [
		'$scope',
		'$http',
		'$q',
		'View',
		'Patient',
		'PatientInsurance',
		'PatientConst',
		'CaseRegistrationConst',
		'Insurances',

		function ($scope, $http, $q, View, Patient, PatientInsurance, PatientConst, CaseRegistrationConst, Insurances) {

			var vm = this;
			vm.isShowForm = false;
			vm.errors = null;
			vm.insuranceTitles = PatientConst.INSURANCE_TITLES;
			vm.insurances = Insurances;
			vm.error_model_insurance = 'patient_insurance';

			$scope.patientConst = PatientConst;
			$scope.caseRegistrationConst = CaseRegistrationConst;
			$scope.view = View;

			vm.init = function (PatientId) {
				$http.get('/api/patients/patient/' + PatientId).then(function (result) {
					vm.patient = new Patient(result.data);
					vm.model = vm.patient;
					validate();
				});
			};

			vm.edit = function() {
				vm.isShowForm = true;
				vm.originalPatient = angular.copy(vm.patient);
			};

			vm.cancel = function() {
				vm.patient = vm.originalPatient;
				vm.model = vm.patient;
				vm.isShowForm = false;
			};

			vm.save = function() {
				Insurances.checkRelationship(vm.patient);
				$http.post('/api/patients/saveInsurances/' + vm.patient.id, $.param({
					data: JSON.stringify(vm.patient)
				})).then(function (res) {
					if (res.data.errors) {
						vm.errors = res.data.errors;
					} else {
						vm.isShowForm = false;
						vm.init(vm.patient.id);
					}
				});
			};

			vm.addInsurance = function(index) {
				vm.patient.insurances.push(Insurances.getPatientInsuranceMaster());
			};

			vm.getView = function () {
				var view = 'app/insurance/' + (vm.isShowForm ? 'edit' : 'view') + '.html';
				return View.get(view);
			};

			vm.hasInsurancesValidationErrors = function() {
				if (vm.errors && vm.errors.patient_insurance) {
					return true;
				}

				return false;
			};


			 function validate() {
				$http.post('/api/patients/validate/' + vm.patient.id, $.param({data: JSON.stringify(vm.patient)})).then(function (result) {
					vm.errors =  angular.fromJson(result.data.errors);
					if (vm.patient.show_insurance_banner) {
						if (vm.hasInsurancesValidationErrors()) {
							vm.edit();
						} else {
							$http.get('/api/patients/resetInsuranceBanner/' + vm.patient.id).then( function() {
								vm.init(vm.patient.id);
								if (!vm.errors.patient) {
									$scope.dialog(View.get('app/profile/profile_complete_modal.html'), $scope, {size: 'sm'});
								}
							});
						}
					}
				});
			};

		}]);

})(opakeApp, angular);
