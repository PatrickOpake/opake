(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('AnalyticsSmsLogCtrl', [
		'$scope',
		'$http',
		'$controller',
		function ($scope, $http, $controller) {

			var vm = this;

			$controller('ListCrtl', {vm: vm});

			vm.search = function () {
				$http.get('/analytics/sms-log/ajax/' + $scope.org_id + '/list', {params: vm.search_params}).then(function (response) {
					vm.items = response.data.items;
					vm.total_count = response.data.total_count;
				});
			};
			vm.search();

		}]);

})(opakeApp, angular);