(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('FeeScheduleListCtrl', [
		'$scope',
		'$http',
		'$controller',
		'FeeScheduleConst',
		function ($scope, $http, $controller, FeeScheduleConst) {

			var vm = this;

			vm.siteId = null;
			vm.siteName = '';
			vm.hasFeeSchedule = false;
			vm.isLoading = false;
			vm.hasNoSites = false;
			vm.errors = null;
			vm.isLoading = false;
			vm.feeScheduleConst = FeeScheduleConst;

			$controller('ListCrtl', {vm: vm, options: {
				defaultParams: {
					type: '1'
				}
			}});

			vm.init = function (siteId) {

				vm.siteId = siteId;

				$http.get('/clients/sites/ajax/' + $scope.org_id + '/fee-schedule/siteInfo/' + vm.siteId).then(function (result) {
					if (result.data) {
						vm.hasFeeSchedule = result.data.hasFeeSchedule;
						vm.siteName = result.data.siteName;
					}
				});

				vm.search();

			};

			vm.search = function () {
				vm.isLoading = true;
				$http.get('/clients/sites/ajax/' + $scope.org_id + '/fee-schedule/list/' + vm.siteId + '/', {
					params: vm.search_params
				}).then(function (response) {
					vm.items = response.data.items;
					vm.total_count = response.data.total_count;
				}).finally(function() {
					vm.isLoading = false;
				});
			};

			vm.uploadFeeSchedule = function(files) {
				var feeScheduleFile = files[0];
				if (feeScheduleFile) {
					vm.errors = null;
					var fd = new FormData();
					fd.append('file', feeScheduleFile);
					fd.append('type', vm.search_params.type);
					$http.post('/clients/sites/ajax/' + $scope.org_id + '/fee-schedule/uploadFeeSchedule/' + vm.siteId + '/', fd, {
						withCredentials: true,
						headers: {
							'Content-Type': undefined
						},
						transformRequest: angular.identity
					}).then(function (result) {
						if (result.data.errors) {
							vm.errors = result.data.errors;
						} else {
							vm.init(vm.siteId);
						}
					});
				}
			};

		}]);

})(opakeApp, angular);
