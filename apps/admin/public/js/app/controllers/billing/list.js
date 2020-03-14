// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BillingListCrtl', [
		'$scope',
		'$http',
		'$controller',
		'$window',
		'Billing',
		'BillingConst',
		'BillingNotes',
		'View',
		function ($scope, $http, $controller, $window, Billing, BillingConst, BillingNotes, View) {
			$scope.billingConst = BillingConst;

			var vm = this;
			vm.isShowLoading = false;
			$controller('ListCrtl', {vm: vm});

			vm.search = function (dontShowLoading) {
				if (!dontShowLoading) {
					vm.isShowLoading = true;
				}

				var params = angular.copy(vm.search_params);

				var insurances = [];
				if (vm.search_params.insurances && vm.search_params.insurances.length) {
					angular.forEach(vm.search_params.insurances, function(insurance) {
						insurances.push(insurance);
					});
				}
				params.insurances = JSON.stringify(insurances);

				$http.get('/billings/ajax/' + $scope.org_id + '/', {params: params}).then(function (response) {
					var items = [];
					var caseIds = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new Billing(data));
						caseIds.push(data.id);
					});
					vm.items = items;
					vm.total_count = response.data.total_count;

					BillingNotes.getUnreadNotes(caseIds);
					if (!dontShowLoading) {
						vm.isShowLoading = false;
					}
				});
			};

			vm.search();

			vm.redirectToCodingPage = function (caseId) {
				$window.location = '/billings/' + $scope.org_id + '/view/' + caseId;
			};

			vm.isItemStatusContinue = function (caseItem) {
				var isStatusContinue = false;
				if (caseItem.coding_id || (caseItem.verification_status == 1)) {
					isStatusContinue = true;
				}

				return isStatusContinue;
			};

			vm.isItemStatusComplete = function(caseItem) {
				return caseItem.has_claim;
			};

			vm.isItemStatusReady = function (caseItem) {
				return caseItem.is_ready_professional_claim || caseItem.is_ready_institutional_claim;
			};

			vm.sendClaims = function () {
				if (vm.toSelected.length) {
					var caseIds = [];
					angular.forEach(vm.toSelected, function(item) {
						caseIds.push(item.id);
					});
					vm.isShowLoading = true;
					$http.post('/billings/ajax/' + $scope.org_id + '/sendBulkClaims',
						$.param({
							data: angular.toJson({
								cases: caseIds
							})
					})).then(function(response) {
						vm.isShowLoading = false;

						if(response.data.success) {
							var res = response.data.results;
							vm.successClaimResult = [];
							vm.errorClaimResult = [];
							angular.forEach(res, function(claimRes) {
								if (claimRes.success) {
									vm.successClaimResult.push(claimRes);
								} else {
									vm.errorClaimResult.push(claimRes);
								}
							});
							vm.modal = $scope.dialog(View.get('billing/modal-billing-claim-bulk.html'), $scope,  {size: 'md'});
							vm.modal.result.then(function () {

							});

							vm.selectAll = false;
							vm.toSelected = [];
							vm.reset();

						} else if (response.data.errors) {
							vm.errors = response.data.errors;
						}
					});
				}

			};

			vm.addToSelectedAll = function () {
				vm.toSelected = [];
				if (!vm.selectAll) {
					angular.forEach(vm.items, function (item) {
						if(vm.isItemStatusReady(item)) {
							vm.toSelected.push(item);
						}
					});
				}
				vm.selectAll = !vm.selectAll;
			};

			vm.newInsurance = function (name) {
				if (name) {
					return {
						id: null,
						name: name,
						isCustomAdded: true
					};
				}

				return undefined;
			};

		}]);

})(opakeApp, angular);
