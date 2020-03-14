// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BillingLedgerListCtrl', [
		'$scope',
		'$http',
		'$controller',
		'BillingConst',
		function ($scope, $http, $controller, BillingConst) {

			$scope.BillingConst = BillingConst;

			var vm = this;
			vm.isShowLoading = true;
			vm.isInitLoading = true;

			$controller('ListCrtl', {vm: vm});

			vm.search = function () {
				vm.isShowLoading = true;
				var paramsData = angular.copy(vm.search_params);
				$http.get('/billings/ledger/ajax/' + $scope.org_id + '/patients', {params: paramsData}).then(function (response) {
					vm.items = response.data.items;
					vm.total_count = response.data.total_count;

					vm.isShowLoading = false;
					vm.isInitLoading = false;
				});
			};

			vm.search();

		}]);

})(opakeApp, angular);
