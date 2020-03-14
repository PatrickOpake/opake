(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ViewAppointmentFormsInfluenzaCtrl', [
		'$controller',
		'$http',
		'$state',
		'$stateParams',
		function ($controller, $http, $state, $stateParams) {

			var appointmentId = $stateParams.appointment;

			var vm = this;
			vm.loadFormSrc = '/api/appointments/forms/influenza/getForm?registration_id=' + appointmentId;
			$controller('CasesFormsInfluenzaCtrl', {vm: vm});

			vm.save = function() {
				vm.errors = null;
				$http.post('/api/appointments/forms/influenza/saveForm?registration_id=' + appointmentId, $.param({data: angular.toJson(vm.form)})).then(function (res) {
					if (res.data.success) {
						$state.go('app.view-appointment', null, {
							reload: true
						});
					} else if (res.data.errors) {
						vm.errors = res.data.errors;
					}
				});
			};

			vm.cancel = function() {
				$state.go('app.view-appointment', null, {
					reload: true
				});
			};

			vm.init();
		}]);

})(opakeApp, angular);
