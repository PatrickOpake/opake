// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ClaimsProcessingProcessListCtrl', [
		'$rootScope',
		'$scope',
		'$http',
		'$controller',
		'View',
		'BillingConst',
		function ($rootScope, $scope, $http, $controller, View, BillingConst) {

			$scope.BillingConst = BillingConst;
			$scope.view = View;

			var vm = this;
			vm.isShowLoading = true;
			vm.isInitLoading = true;
			vm.selectedBunchId = null;

			$controller('ListCrtl', {vm: vm});

			vm.init = function() {

				$rootScope.subTopMenu = {
					'process': 'Process',
					'processed': 'Processed',
					'resubmitted': 'Resubmitted',
					'onHold': 'On Hold',
					'exception': 'Exception'
				};

				$rootScope.subTopMenuActive = 'process';

				vm.search();
			};

			vm.search = function () {
				vm.toSelected = [];
				vm.isShowLoading = true;
				var params = angular.copy(vm.search_params);
				$http.get('/billings/claims-processing/ajax/' + $scope.org_id + '/bunches', {params: params}).then(function (response) {
					vm.items = response.data.items;
					vm.total_count = response.data.total_count;
					vm.isShowLoading = false;
					vm.isInitLoading = false;
				});
			};

			vm.selectBunch = function(bunch) {
				vm.selectedBunchId = bunch.id;
				if (bunch.status == 1) {
					bunch.status_text = 'In Progress';
					$http.post('/billings/claims-processing/ajax/' + $scope.org_id + '/changeBunchStatus/' + bunch.id, $.param({
						newStatus: 2
					}));
				}
				$scope.$broadcast('bunchSelected', bunch);
			};

			vm.isBunchSelected = function(bunch) {
				return (vm.selectedBunchId !== null && vm.selectedBunchId == bunch.id);
			};

			$scope.$on('paymentsProcessed', function() {
				$http.post('/billings/claims-processing/ajax/' + $scope.org_id + '/checkBunchProcessed/' + vm.selectedBunchId).then(function() {
					vm.search();
				});
			});

		}]);

})(opakeApp, angular);
