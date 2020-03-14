// Operative Report Surgeons
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('OperativeReportSurgeonsCrtl', [
		'$rootScope',
		'$scope',
		'$http',
		'$controller',
		'$location',
		'$window',
		function ($rootScope, $scope, $http, $controller, $location, $window) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.search = function () {
				var data = vm.search_params;
				data.for_op_reports_list = true;
				$http.get('/operative-reports/ajax/' + $scope.org_id + '/surgeons', {params: data }).then(function (response) {
					var data = response.data;
					vm.items = data.items;
					vm.total_count = data.total_count;
				});
			};
			vm.search();

			vm.viewSurgeonReports = function(item, type) {
				if(type === 'setting') {
					$window.location = '/operative-reports/' + $scope.org_id + '/index/' + item.id;
				} else {
					$window.location = '/operative-reports/my/' + $scope.org_id + '/index/' + item.id;
				}

			};

			vm.showSurgeonItem = function (item) {
				return !($rootScope.loggedUser.isFullAdmin()
					&& $rootScope.loggedUser.id === item.id
					&& !$rootScope.loggedUser.is_enabled_op_report)
					|| ($rootScope.loggedUser.isSatelliteOffice() && item.is_enabled_op_report);
			};

		}]);

})(opakeApp, angular);
