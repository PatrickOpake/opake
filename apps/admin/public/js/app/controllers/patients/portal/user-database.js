(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PatientsPortalUserDatabaseCtrl', [
		'$rootScope',
		'$scope',
		'$http',
		'PatientUser',

		function ($rootScope, $scope, $http, PatientUser) {

			var vm = this;
			vm.search_params = {};
			vm.totalCount = null;
			vm.items = [];

			vm.isDataLoaded = false;

			vm.init = function() {
				vm.reset();
			};

			vm.search = function() {
				$http.get('/patient-users/internal/ajax/search', {params: vm.search_params}).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new PatientUser(data));
					});
					vm.items = items;
					vm.totalCount = response.data.total_count;
					vm.isDataLoaded = true;
				});
			};

			vm.reset = function() {
				vm.search_params = {
					p: 0,
					l: 20,
					sort_by: 'first_login_date',
					sort_order: 'DESC'
				};
				vm.search();
			};

			vm.init();

		}]);

})(opakeApp, angular);
