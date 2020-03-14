// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ClaimsProcessingOtherPaymentsListCtrl', [
		'$rootScope',
		'$scope',
		'$http',
		'$controller',
		'BillingConst',
		function ($rootScope, $scope, $http, $controller, BillingConst) {

			$scope.BillingConst = BillingConst;

			var vm = this;
			vm.isShowLoading = true;
			vm.isInitLoading = true;
			vm.tableType = '';
			vm.showResubmitButton = true;

			$controller('ListCrtl', {vm: vm});

			vm.init = function(tableType) {
				vm.tableType = tableType;
				if (vm.tableType === 'resubmitted' || vm.tableType === 'processed') {
					vm.showResubmitButton = false;
				}
				vm.search();
			};

			vm.search = function () {
				vm.selectAll = false;
				vm.toSelected = [];
				vm.isShowLoading = true;
				var params = angular.copy(vm.search_params);
				$http.get('/billings/claims-processing/ajax/' + $scope.org_id + '/paymentsTable/' + vm.tableType, {params: params}).then(function (response) {
					vm.items = response.data.items;
					vm.total_count = response.data.total_count;
					vm.isShowLoading = false;
					vm.isInitLoading = false;
				});
			};

			vm.printSelected = function() {

			};

			vm.resubmitSelected = function() {
				if (vm.toSelected.length) {
					vm.isShowLoading = true;
					var paymentIds = [];
					angular.forEach(vm.toSelected, function(payment) {
						paymentIds.push(payment.id);
					});
					$http.post('/billings/claims-processing/ajax/' + $scope.org_id + '/resubmitClaims/', $.param({
						data: angular.toJson({
							payments: paymentIds
						})
					})).then(function(res) {
						vm.isShowLoading = false;
						if (res.data.success) {
							vm.search();
						}
					});
				}
			};

			vm.toggleShowServices = function(payment) {
				payment._isShowServices = !payment._isShowServices;
			};

		}]);

})(opakeApp, angular);
