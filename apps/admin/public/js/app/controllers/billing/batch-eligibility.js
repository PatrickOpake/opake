// Batch eligibility
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BatchEligibilityCtrl', [
		'$scope',
		'$http',
		'config',
		'View',
		function ($scope, $http, config, View) {

			var vm = this;

			vm.search_params = {};
			vm.toCheckEligibilityList = [];
			vm.batchEligibilities = [];
			vm.selectCasesAll = false;
			vm.isEligibilityChecking = false;
			vm.caseInsurancesViewDetails = [];


			vm.createBatch = function () {
				var data = angular.copy(vm.search_params);
				if (data.dos_from) {
					data.dos_from = moment(data.dos_from).format('YYYY-MM-DD');
				}
				if (data.dos_to) {
					data.dos_to = moment(data.dos_to).format('YYYY-MM-DD');
				}
				$http.get('/billings/batch-eligibility/ajax/' + $scope.org_id + '/cases/', {params: data}).then(function (response) {
					vm.cases = response.data;
					vm.createBatchModal = $scope.dialog(View.get('billing/batch-eligibility/create-batch.html'), $scope, {windowClass: '', size: 'lg'});
				});

			};

			vm.searchBatchEligibilities = function () {
				$http.get('/billings/batch-eligibility/ajax/' + $scope.org_id + '/items/').then(function (response) {
					vm.batchEligibilities = [];
					angular.forEach(response.data, function (item) {
						item.date_received = moment(item.date_received).toDate();
						vm.batchEligibilities.push(item);
					});
				});
			};

			vm.searchBatchEligibilities();

			vm.clear = function () {
				for (var key in vm.search_params) {
					delete vm.search_params[key];
				}
			};

			vm.checkEligibility = function () {
				vm.eligibleCoverage = null;
				vm.eligibleErrors = null;
				var insurancesIds = [];
				vm.isEligibilityChecking = true;

				angular.forEach(vm.toCheckEligibilityList, function (item) {
					insurancesIds.push(item.id);
				});

				var params = {
					'case_insurances_id': insurancesIds,
					'organization_id': $scope.org_id
				};

				return $http.post('/insurances/ajax/eligible/batchChecking', $.param({data: JSON.stringify(params)})).then(function (result) {
					if (result.data.success) {
						vm.eligibleCoverage  = result.data.coverage;
						vm.createBatchModal.close();
						vm.searchBatchEligibilities();
						vm.isEligibilityChecking = false;

					} else {
						vm.isEligibilityChecking = false;
						vm.eligibleErrors = result.data.errors;
					}
				}, function() {
					vm.isEligibilityChecking = false;
				});
			};

			vm.addToCheckEligibilityList = function (item) {
				var idx = vm.toCheckEligibilityList.indexOf(item);
				if (idx > -1) {
					vm.toCheckEligibilityList.splice(idx, 1);
					if(!vm.toCheckEligibilityList.length) {
						vm.selectCasesAll = false;
					}
				} else {
					vm.toCheckEligibilityList.push(item);
				}
				vm.selectCasesAll = (vm.cases && (vm.cases.length == vm.toCheckEligibilityList.length));
			};

			vm.isAddedToCheckEligibilityList = function(item) {
				return vm.toCheckEligibilityList.indexOf(item) > -1;
			};

			vm.addToCheckEligibilityListAll = function () {
				vm.toCheckEligibilityList = [];
				if (!vm.selectCasesAll) {
					angular.forEach(vm.cases, function (item) {
						vm.toCheckEligibilityList.push(item);
					});
				}
				vm.selectCasesAll = !vm.selectCasesAll;
			};

			vm.viewBatch = function (item) {
				$http.get('/billings/batch-eligibility/ajax/' + $scope.org_id + '/viewDetail/' + item.id).then(function (result) {
					vm.caseInsurancesViewDetails = result.data;
					$scope.dialog(View.get('billing/batch-eligibility/view-batch-details.html'), $scope, {windowClass: '', size: 'lg'}).result.then(function () {

					});
				});

			};

		}]);

})(opakeApp, angular);
