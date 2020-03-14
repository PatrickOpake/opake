(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('AppCtrl', [
		'$state',
		'$http',
		'loggedUser',
		'InactivityReminder',
		function ($state, $http, loggedUser, InactivityReminder) {
			var vm = this;
			vm.loggedUser = loggedUser;

			if (vm.loggedUser) {
				InactivityReminder.init({
					logout: function() {
						$state.go('app.logout');
					},
					stayOnline: function() {
						$http.post('/api/auth/refreshExpires');
					},
					keepActive: function() {
						$http.post('/api/auth/keepActive');
					},
					checkLoggedIn: function() {
						$http.post('/api/auth/checkLoggedIn').then(function(res) {
							if (!res.data.logged) {
								location.reload();
							}
						});
					}
				});
			}

		}]);

})(opakeApp, angular);
