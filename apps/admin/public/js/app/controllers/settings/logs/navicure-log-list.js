(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('SettingsLogsNavicureLogList', [
		'$scope',
		'$http',
		'$controller',
		'BillingConst',
		function ($scope, $http, $controller, BillingConst) {

			$scope.BillingConst = BillingConst;

			var vm = this;

			$controller('ListCrtl', {vm: vm});

			vm.search = function () {
				$http.get('/settings/logs/navicure/ajax', {params: vm.search_params}).then(function (response) {
					vm.items = response.data.items;
					vm.total_count = response.data.total_count;
				});
			};
			vm.search();

		}]);

})(opakeApp, angular);