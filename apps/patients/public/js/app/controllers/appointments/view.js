(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ViewAppointmentCtrl', [
		'$scope',
		'$http',
		'$stateParams',
		'CaseRegistrationConst',
		'CaseRegistration',
		function ($scope, $http, $stateParams, CaseRegistrationConst, CaseRegistration) {

			$scope.caseRegistrationConst = CaseRegistrationConst;

			var vm = this;
			var appointmentId = $stateParams.appointment;
			vm.appointment = null;
			vm.isShowFormSelection = false;

			vm.toggleFormSelection = function() {
				vm.isShowFormSelection = !vm.isShowFormSelection;
			};

			vm.loadAppointment = function() {
				$http.get('/api/appointments/get', {params: {'id': appointmentId}}).then(function (res) {
					if (res.data.success) {
						vm.appointment = new CaseRegistration(res.data.registration);
					}
				});
			};

			vm.loadAppointment();
		}]);

})(opakeApp, angular);
