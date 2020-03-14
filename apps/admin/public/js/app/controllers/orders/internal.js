// Internal Order list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('InternalOrderListCrtl', [
		'$scope',
		'$http',
		'$controller',
		'OrderInternal',
		function ($scope, $http, $controller, OrderInternal) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.search = function () {
				var data = vm.search_params;

				$http.get('/orders/ajax/internal/index', {params: data}).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new OrderInternal(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
				});
			};
			vm.search();

		}]);

})(opakeApp, angular);
