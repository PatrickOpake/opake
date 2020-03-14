// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BillingLedgerBulkPatientSearchCtrl', [
		'$scope',
		'$http',
		'$controller',
		'BillingConst',
		function ($scope, $http, $controller, BillingConst) {

			$scope.BillingConst = BillingConst;

			var vm = this;
			vm.isShowLoading = true;
			vm.isInitLoading = true;

			vm.init = function(currentPatientId, currentSourceId) {
				$controller('ListCrtl', {vm: vm, options: {
					defaultParams: {
						exclude_patient: currentPatientId,
						insurance: currentSourceId.patient_insurance_id,
						l: 10
					}
				}});

				vm.search();
			};

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

		}]);

})(opakeApp, angular);
