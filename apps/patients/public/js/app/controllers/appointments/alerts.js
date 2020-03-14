(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('AppointmentAlertsCtrl', [
		'$rootScope',
		'$http',
		'$state',
		'View',
		function ($rootScope, $http, $state, View) {
			var vm = this;

			vm.alerts = [];
			vm.updateAlerts = function() {
				$http.get('/api/appointments/alerts/getAlerts').then(function(res) {
					if (res.data.success) {
						vm.alerts = res.data.result;
					}
				})
			};

			vm.hideAlert = function(alert) {
				var appointmentId = alert.appointment_id;
				var type = alert.type;
				$http.post('/api/appointments/alerts/hideAlert', $.param({id: appointmentId, type: type})).then(function(res) {
					if (res.data.success) {
						vm.updateAlerts();
					}
				});
			};

			vm.getAlertsWindowTitle = function() {
				return '' + vm.alerts.length + ' ' + (vm.alerts.length == 1 ? 'Alert' : 'Alerts');
			};

			vm.openAppointment = function(alert) {
				return $state.go(alert.link.state, alert.link.params);
			};

			vm.getView = function() {
				var view = 'app/appointments/alerts.html';
				return View.get(view);
			};

			$rootScope.$on('changeAppointmentConfirmStatus', function() {
				vm.updateAppointmentsAlerts();
			});

			vm.updateAlerts();

		}]);

})(opakeApp, angular);
