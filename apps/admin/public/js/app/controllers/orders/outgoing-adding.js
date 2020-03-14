// Outgoing Order Adding
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('OrderOutgoingAddingCrtl', [
		'$scope',
		'$http',
		'$window',
		'config',
		function ($scope, $http, $window, config) {

			var vm = this;

			var order_id = this;

			vm.search_items = [];
			vm.total_count = 0;
			vm.search_params = {p: 0, l: config.pagination.limit};

			vm.selection = [];


			vm.init = function (id) {
				order_id = id;
			};

			vm.search = function () {
				$http.get('/inventory/ajax/' + $scope.org_id + '/list/', {params: angular.extend(vm.search_params, {org_id: $scope.org_id})}).then(function (response) {
					var data = response.data;
					vm.search_items = data.items;
					vm.total_count = data.total_count;
				});
			};
			vm.search();

			vm.reset = function () {
				for (var key in vm.search_params) {
					delete vm.search_params[key];
				}
				vm.search_params.p = 0;
				vm.search_params.l = config.pagination.limit;
				vm.search();
			};

			vm.save = function () {
				$http.post('/orders/ajax/outgoing/' + $scope.org_id + '/save/' + (order_id ? order_id : ''), $.param({items: vm.selection})).then(function (resp) {
					if (resp.data.id) {
						$window.location = '/orders/outgoing/' + $scope.org_id + '/view/' + resp.data.id;
					}
				});
			};


			vm.toggleSelection = function (id) {
				var idx = vm.selection.indexOf(id);
				if (idx > -1) {
					vm.selection.splice(idx, 1);
				} else {
					vm.selection.push(id);
				}
			};

			vm.resetSelection = function () {
				vm.selection = [];
			};

		}]);

})(opakeApp, angular);
