(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('SettingsICDYearViewCtrl', [
		'$scope',
		'$http',
		'$controller',
		function ($scope, $http, $controller) {

			var vm = this;

			$controller('ListCrtl', {vm: vm});

			vm.init = function (yearId) {
				vm.yearId = yearId;
				$http.get('/settings/databases/icd/ajax/getYearById/' + vm.yearId).then(function (response) {
					vm.year = response.data.year;
				});
				vm.search();
			};

			vm.search = function () {
				$http.get('/settings/databases/icd/ajax/getIcdsForYear/' + vm.yearId, {params: vm.search_params}).then(function (response) {
					vm.items = response.data.items;
					vm.total_count = response.data.total_count;
				});
			};

			vm.activate = function(id) {
				$http.get('/settings/databases/icd/ajax/activate/' + id, {params: {year_id: vm.yearId}}).then(function () {
					vm.search();
				});
			};

			vm.deactivate = function(id) {
				$http.get('/settings/databases/icd/ajax/deactivate/' + id, {params: {year_id: vm.yearId}}).then(function () {
					vm.search();
				});
			};

		}]);

})(opakeApp, angular);
