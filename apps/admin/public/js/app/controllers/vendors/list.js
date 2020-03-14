// Vendor list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('VendorListCrtl', [
		'$scope',
		'$http',
		'$controller',
		'Vendor',
		'VendorConst',
		function ($scope, $http, $controller, Vendor, VendorConst) {
			$scope.vendorConst = VendorConst;

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.search = function () {
				var data = vm.search_params;
				var query;
				if ($scope.org_id) {
					query = $http.get('/vendors/ajax/' + $scope.org_id + '/list', {params: data })
				} else {
					query = $http.get('/vendors/internal-vendors/ajax/list', {params: data })
				}

				query.then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new Vendor(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
				});
			};
			vm.search();

		}]);

})(opakeApp, angular);
