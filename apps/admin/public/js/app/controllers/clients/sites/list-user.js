(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('SiteUserListCrtl', [
		'$scope',
		'$http',
		'$controller',
		'Site',
		function ($scope, $http, $controller, Site) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.search = function () {
				var data = vm.search_params;
				$http.get('/sites/ajax/' + $scope.org_id + '/index/', {params: data }).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new Site(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
				});
			};
			vm.search();

		}]);

})(opakeApp, angular);
