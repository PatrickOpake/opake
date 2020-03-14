// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BillingLedgerStatementHistoryListCtrl', [
		'$scope',
		'$http',
		'$controller',
		function ($scope, $http, $controller) {

			var vm = this;
			vm.isShowLoading = true;
			vm.isInitLoading = true;

			$controller('ListCrtl', {vm: vm});

			vm.search = function () {
				vm.isShowLoading = true;
				var paramsData = angular.copy(vm.search_params);

				if (paramsData.date_generated_from) {
					paramsData.date_generated_from = moment(paramsData.date_generated_from).format('YYYY-MM-DD');
				}
				if (paramsData.date_generated_to) {
					paramsData.date_generated_to = moment(paramsData.date_generated_to).format('YYYY-MM-DD');
				}
				$http.get('/billings/ledger/ajax/' + $scope.org_id + '/statementHistory', {params: paramsData}).then(function (response) {
					vm.items = [];
					angular.forEach(response.data.items, function (item) {
						item.date_generated = moment(item.date_generated).toDate();
						item.dob = moment(item.dob).toDate();
						vm.items.push(item);
					});
					vm.total_count = response.data.total_count;
					vm.isShowLoading = false;
					vm.isInitLoading = false;
				});
			};

			vm.search();

		}]);

})(opakeApp, angular);
