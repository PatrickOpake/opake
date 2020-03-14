// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ClaimsManagementCtrl', [
		'$scope',
		'$http',
		'$controller',
		'BillingConst',
		'Tools',
		function ($scope, $http, $controller, BillingConst, Tools) {

			$scope.BillingConst = BillingConst;

			var vm = this;
			vm.isShowLoading = true;
			vm.isInitLoading = true;
			vm.isPrintRunning = false;
			vm.isForceUpdateRunning = false;

			$controller('ListCrtl', {vm: vm, options: {
				defaultParams: {
					sort_by: 'claim_id',
					sort_order: 'DESC'
				}
			}});

			vm.search = function () {
				vm.toSelected = [];
				vm.isShowLoading = true;
				var paramsData = angular.copy(vm.search_params);
				if (paramsData.payer) {
					paramsData.payer = paramsData.payer.id;
				}
				$http.get('/billings/claims-management/ajax/' + $scope.org_id + '/', {params: paramsData}).then(function (response) {
					vm.items = response.data.items;
					vm.total_count = response.data.total_count;
					vm.isShowLoading = false;
					vm.isInitLoading = false;
				});
			};

			vm.printSelected = function() {
				if (!vm.isPrintRunning && vm.toSelected && vm.toSelected.length) {
					vm.isPrintRunning = true;
					vm.isShowLoading = true;
					var documents = [];
					angular.forEach(vm.toSelected, function (item) {
						documents.push(item.id);
					});
					$http.post('/billings/claims-management/ajax/' + $scope.org_id + '/compileCodingDocuments', $.param({claims: documents, type: 'electronic'})).then(function (res) {
						vm.isShowLoading = false;
						vm.isPrintRunning = false;
						if (res.data.success) {
							Tools.print(location.protocol + '//' + location.host + res.data.url);
						}
					}, function() {
						vm.isShowLoading = false;
						vm.isPrintRunning = false;
					});
				}
			};

			vm.forceUpdate = function() {
				if (!vm.isForceUpdateRunning) {
					vm.isForceUpdateRunning = true;
					vm.isShowLoading = true;
					$http.post('/cases/ajax/coding/claim/' + $scope.org_id + '/forceUpdateStatus').then(function(response) {
						vm.isShowLoading = false;
						vm.isForceUpdateRunning = false;

						vm.search();
					});
				}
			};

			vm.search();

		}]);

})(opakeApp, angular);
