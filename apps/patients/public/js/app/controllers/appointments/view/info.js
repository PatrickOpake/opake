(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ViewAppointmentInfoCtrl', [
		'$scope',
		'$q',
		'$http',
		'$state',
		'$stateParams',
		'View',
		'PatientConst',
		'CaseRegistration',
		function ($scope, $q, $http, $state, $stateParams, View, PatientConst, CaseRegistration) {

			$scope.patientConst = PatientConst;

			var appointmentId = $stateParams.appointment;
			var vm = this;
			vm.isShowForm = false;
			vm.patient = null;
			vm.originalPatient = null;
			vm.errors = null;

			vm.loadPatient = function() {
				var def = $q.defer();
				$http.get('/api/appointments/get', {params: {'id': appointmentId}}).then(function (res) {
					if (res.data.success) {
						vm.originalPatient = null;
						vm.patient = new CaseRegistration(res.data.registration);
						def.resolve(vm.patient);
					}
				});

				return def.promise;
			};

			vm.confirm = function() {
				vm.errors = null;
				$http.post('/api/appointments/confirmPatientInfo?id=' + vm.patient.id, $.param({data: JSON.stringify(vm.patient)})).then(function (result) {
					if (result.data.success) {
						$state.go('app.view-appointment', null, {
							reload: true
						});
					} else if (result.data.errors) {
						vm.errors = result.data.errors;
					}
				});
			};

			vm.edit = function() {
				vm.errors = null;
				vm.originalPatient = angular.copy(vm.patient);
				vm.isShowForm = true;
			};

			vm.cancel = function() {
				vm.errors = null;
				vm.patient = vm.originalPatient;
				vm.originalPatient = null;
				vm.isShowForm = false;
			};

			vm.save = function() {
				vm.errors = null;
				$http.post('/api/appointments/savePatientInfo?id=' + vm.patient.id, $.param({data: JSON.stringify(vm.patient)})).then(function (result) {
					if (!result.data.success) {
						if (result.data.errors) {
							vm.errors =  result.data.errors;
						}
					} else {
						vm.loadPatient().then(function() {
							vm.isShowForm = false;
						});

					}
				});
			};

			vm.getView = function() {
				return View.get(vm.isShowForm ? 'app/appointments/view/info/form.html' : 'app/appointments/view/info/view.html');
			};

			vm.loadPatient();

		}]);

})(opakeApp, angular);
