// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PaperClaimsCtrl', [
		'$scope',
		'$http',
		'$controller',
		'BillingConst',
		'Tools',
		'PaperClaim',
		'BillingNotes',
		function ($scope, $http, $controller, BillingConst, Tools, PaperClaim, BillingNotes) {

			$scope.BillingConst = BillingConst;

			var vm = this;
			vm.isShowLoading = true;
			vm.isInitLoading = true;
			vm.isPrintRunning = false;

			$controller('ListCrtl', {vm: vm, options: {
				defaultParams: {
					sort_by: 'claim_id',
					sort_order: 'DESC'
				}
			}});

			vm.search = function () {
				vm.toSelected = [];
				vm.isShowLoading = true;
				var caseIds = [];
				var paramsData = angular.copy(vm.search_params);
				if (paramsData.payer) {
					paramsData.payer = paramsData.payer.id;
				}
				$http.get('/billings/claims-management/ajax/' + $scope.org_id + '/paperClaims', {params: paramsData}).then(function (response) {
					vm.items = [];
					angular.forEach(response.data.items, function (item) {
						var claim = new PaperClaim(item);
						vm.items.push(claim);
						caseIds.push(item.id);
					});
					vm.total_count = response.data.total_count;
					vm.isShowLoading = false;
					vm.isInitLoading = false;
					BillingNotes.getUnreadNotes(caseIds);
				});
			};

			vm.printSelected = function() {
				if (!vm.isPrintRunning && vm.toSelected && vm.toSelected.length) {
					vm.isPrintRunning = true;
					vm.isShowLoading = true;
					var documents = [];
					angular.forEach(vm.toSelected, function (item) {
						documents.push(item.claim_id);
					});
					$http.post('/billings/claims-management/ajax/' + $scope.org_id + '/compileCodingDocuments', $.param({claims: documents, type: 'paper'})).then(function (res) {
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

			vm.search();

		}]);

})(opakeApp, angular);
