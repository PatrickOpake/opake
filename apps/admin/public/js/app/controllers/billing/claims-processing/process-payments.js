// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ClaimsProcessingProcessPaymentsListCtrl', [
		'$rootScope',
		'$scope',
		'$http',
		'$controller',
		'BillingConst',
		function ($rootScope, $scope, $http, $controller, BillingConst) {

			$scope.BillingConst = BillingConst;

			var vm = this;
			vm.isShowLoading = false;
			vm.isBunchLoading = false;
			vm.selectedBunchId = null;
			vm.isBunchPaymentsLoaded = false;


			$controller('ListCrtl', {vm: vm});

			vm.search = function () {
				vm.selectAll = false;
				vm.toSelected = [];
				vm.isShowLoading = true;
				var params = angular.copy(vm.search_params);
				$http.get('/billings/claims-processing/ajax/' + $scope.org_id + '/payments/' + vm.selectedBunchId, {params: params}).then(function (response) {
					vm.items = response.data.items;
					vm.total_count = response.data.total_count;
					vm.isShowLoading = false;
					vm.isBunchLoading = false;
					vm.isBunchPaymentsLoaded = true;
				});
			};

			vm.printSelected = function() {

			};

			vm.processSelected = function() {
				if (vm.toSelected.length) {
					var paymentIds = [];
					angular.forEach(vm.toSelected, function(payment) {
						paymentIds.push(payment.id);
					});
					$http.post('/billings/claims-processing/ajax/' + $scope.org_id + '/processClaims/', $.param({
						data: angular.toJson({
							payments: paymentIds
						})
					})).then(function(res) {
						if (res.data.success) {
							vm.search();
							$scope.$emit('paymentsProcessed');
						}
					});
				}
			};

			vm.statusChanged = function(payment) {
				$http.post('/billings/claims-processing/ajax/' + $scope.org_id + '/changePaymentStatus/' + payment.id, $.param({
					newStatus: payment.status
				}));
			};

			vm.toggleShowServices = function(payment) {
				payment._isShowServices = !payment._isShowServices;
			};

			$scope.$on('bunchSelected', function(e, bunch) {
				vm.selectedBunchId = bunch.id;
				vm.isBunchPaymentsLoaded = false;
				vm.search();
			});

		}]);

})(opakeApp, angular);
