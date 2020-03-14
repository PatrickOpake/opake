(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('AuthCtrl', [
		'$state',
		'AuthService',
		function ($state, AuthService) {
			var vm = this;

			vm.initForm = function (form) {
				vm.loginData = {};
				vm.changePassData = {};
				vm.suggestion = '';
				vm.currentForm = form;
			};
			vm.initForm('login');

			vm.login = function () {
				AuthService.login(vm.loginData).then(function (result) {
					if (result.data.change_password) {
						vm.initForm('change_password');
					} else {
						goToPortal();
					}
				}, function (error) {
					vm.suggestion = error.data.suggestion;
				});
			};

			vm.changePassword = function () {
				AuthService.changePassword(vm.changePassData).then(function (result) {
					goToPortal();
				}, function (error) {
					vm.suggestion = error.data.suggestion;
				});
			};

			vm.initForm = function (form) {
				vm.loginData = {};
				vm.changePassData = {};
				vm.suggestion = '';
				vm.currentForm = form;
			};

			function goToPortal() {
				AuthService.reset();
				$state.go('app.home');
			}

		}]);

})(opakeApp, angular);
