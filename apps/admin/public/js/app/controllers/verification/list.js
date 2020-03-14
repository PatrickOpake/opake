// Verification list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('VerificationListCrtl', [
		'$scope',
		'$http',
		'$controller',
		'$window',
		'$location',
		'VerificationListCase',
		'VerificationConst',
		'Permissions',

		function ($scope, $http, $controller, $window, $location, VerificationListCase, VerificationConst, Permissions) {
			$scope.verificationConst = VerificationConst;

			var vm = this;
			vm.isShowLoading = false;

			$controller('ListCrtl', {vm: vm});

			vm.hasCaseManagementAccess = Permissions.hasAccess('case_management', 'view');

			vm.search = function () {
				vm.isShowLoading = true;
				var params = $location.search();
				if (params.p && params.l) {
					vm.search_params.p = parseInt(params.p, 10);
					vm.search_params.l = parseInt(params.l, 10);
					$window.location.hash = '';
				}
				var data = angular.copy(vm.search_params);
				if (data.start) {
					data.start = moment(data.start).format('YYYY-MM-DD');
				}
				if (data.end) {
					data.end = moment(data.end).format('YYYY-MM-DD');
				}
				return $http.get('/verification/ajax/' + $scope.org_id + '/index/', {params: data}).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new VerificationListCase(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
					vm.isShowLoading = false;
				});
			};

			vm.search();

			vm.view = function (regId) {
				$window.location = '/verification/' + $scope.org_id + '/view/' + regId + '#?p=' + vm.search_params.p + '&l=' + vm.search_params.l;
			};

		}]);

})(opakeApp, angular);
