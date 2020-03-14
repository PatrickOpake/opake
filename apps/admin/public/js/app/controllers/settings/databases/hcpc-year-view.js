(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('SettingsHCPCYearViewCtrl', [
		'$scope',
		'$http',
		'$controller',
		function ($scope, $http, $controller) {

			var vm = this;
			vm.isLoading = true;
			vm.errors = null;

			$controller('ListCrtl', {vm: vm});

			vm.init = function (yearId) {
				vm.yearId = yearId;
				$http.get('/settings/databases/hcpc/ajax/getYearById/' + vm.yearId).then(function (response) {
					vm.year = response.data.year;
				});
				vm.search();
			};

			vm.search = function () {
				$http.get('/settings/databases/hcpc/ajax/getHcpcForYear/' + vm.yearId, {params: vm.search_params}).then(function (response) {
					vm.items = response.data.items;
					vm.total_count = response.data.total_count;
					vm.isLoading = false;
				});
			};

		}]);

})(opakeApp, angular);
