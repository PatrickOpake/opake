// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BillingLedgerPaymentActivityCtrl', [
		'$scope',
		'$http',
		'$controller',
		'BillingConst',
		'Tools',
		function ($scope, $http, $controller, BillingConst, Tools) {

			var vm = this;

			$controller('ListCrtl', {vm: vm});

			vm.errors = [];
			vm.isInitLoading = true;
			vm.isExportGenerating = false;

			$scope.BillingConst = BillingConst;

			vm.search = function () {
				vm.errors = [];
				vm.selectAll = false;
				vm.toSelected = [];
				var data = angular.copy(vm.search_params);
				if (data.date_from) {
					data.date_from = moment(data.date_from).format('YYYY-MM-DD');
				}
				if (data.date_to) {
					data.date_to = moment(data.date_to).format('YYYY-MM-DD');
				}
				$http.get('/billings/ledger-payment-activity/ajax/' + $scope.org_id + '/', {params: data}).then(function (response) {
					vm.items = response.data.items;
					vm.total_count = response.data.total_count;
					vm.isInitLoading = false;

				});
			};

			vm.search();

			vm.export = function () {
				vm.errors = [];
				vm.isExportGenerating = true;
				var paramsData = angular.copy(vm.search_params);
				$http.get('/billings/ledger-payment-activity/ajax/' + $scope.org_id + '/export/', {params: paramsData}).then(function (response) {
					if (response.data.success) {
						Tools.export(response.data.url);
					}
					else {
						vm.errors = response.data.errors;
					}
					vm.isExportGenerating = false;
				}, function(error) {
					vm.errors = [error.data.message];
					vm.isExportGenerating = false;
				});
			};
		}]);

})(opakeApp, angular);
