(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ViewAppointmentFormsPreOperativeCtrl', [
		'$scope',
		'$http',
		'$state',
		'$stateParams',
		'$controller',
		'CaseRegistrationReconciliation',
		function ($scope, $http, $state, $stateParams, $controller, CaseRegistrationReconciliation) {

			var appointmentId = $stateParams.appointment;
			var vm = this;
			$controller('CasesFormsPreOperativeCtrl', {vm: vm});

			vm.init = function() {
				$http.get('/api/appointments/forms/pre-operative/getForm?appointment=' + appointmentId).then(function (res) {
					if (res.data.success) {
						vm.form = res.data.form;
					} else {
						vm.form = angular.copy(vm.newForm)
					}
				});
			};

			vm.save = function() {
				vm.errors = null;
				var blankReconciliationObject = new CaseRegistrationReconciliation({});
				$http.post('/api/appointments/forms/pre-operative/saveForm?appointment=' + appointmentId, $.param({data: angular.toJson(vm.form), reconciliation: angular.toJson(blankReconciliationObject)})).then(function (res) {
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
