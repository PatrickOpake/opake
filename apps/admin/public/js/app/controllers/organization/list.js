(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('OrganizationListCrtl', [
		'$scope',
		'$http',
		'$controller',
		'Organization',
		function ($scope, $http, $controller, Organization) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.search = function () {
				var data = vm.search_params;
				if (data.user) {
					window.location.replace('/clients/users/?user=' + data.user);
				}
				$http.get('/clients/ajax/index/', {params: data}).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new Organization(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
				});
			};
			vm.search();

		}]);

})(opakeApp, angular);
