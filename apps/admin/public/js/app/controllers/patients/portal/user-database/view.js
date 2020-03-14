(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PatientsPortalUserDatabaseViewCtrl', [
		'$scope',
		'$http',
		'$q',
		'View',
		'PatientUser',
		'PatientsPortal',

		function ($scope, $http, $q, View, PatientUser, PatientsPortal) {

			var vm = this;
			vm.user = null;
			vm.originalUser = null;
			vm.isShowForm = false;
			vm.errors = null;

			vm.init = function(userId) {
				$http.get('/patient-users/internal/ajax/getUser/' + userId).then(function (result) {
					vm.user = new PatientUser(result.data);
				});
			};

			vm.edit = function() {
				vm.isShowForm = true;
				vm.originalUser = angular.copy(vm.user);
			};

			vm.cancel = function() {
				vm.user = vm.originalUser;
				vm.isShowForm = false;
				vm.errors = null;
			};

			vm.save = function() {
				var def = $q.defer();
				$http.post('/patient-users/internal/ajax/save', $.param({data: JSON.stringify(vm.user)})).then(function (result) {
					vm.errors = null;
					if (result.data.id) {
						window.location = '/patient-users/internal/view/' + result.data.id;
						def.resolve();
					} else if (result.data.errors) {
						vm.errors =  result.data.errors.split(';');
						def.reject();
					}
				});

				return def.promise;
			};

			vm.activateUser = function() {
				vm.user.active = true;
			};

			vm.deactivateUser = function() {
				vm.user.active = false;
			};

			vm.getView = function () {
				var view = 'patients/portal/user-database/' + (vm.isShowForm ? 'form' : 'view') + '.html';
				return View.get(view);
			};

			vm.sendPassword = function() {
				$scope.org_id = vm.user.patient.organization.id;
				PatientsPortal.openEmailWindow($scope, vm.user.patient.id).then(function() {
					window.location = '/patient-users/internal/view/' + vm.user.id;
				});
			};

		}]);

})(opakeApp, angular);
