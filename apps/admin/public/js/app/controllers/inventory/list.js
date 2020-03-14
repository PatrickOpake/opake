// Inventory list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('InventoryListCrtl', [
		'$scope',
		'$http',
		'$window',
		'$location',
		'config',
		'InventoryConst',
		'Source',

		function ($scope, $http, $window, $location, config, InventoryConst, Source) {

			var vm = this;
			vm.alert_counts = {};
			vm.search_params = {p: 0, l: config.pagination.limit};
			vm.alert = '';
			vm.order_modal = null;
			vm.inventoryConst = InventoryConst;

			$scope.$on("$locationChangeSuccess", function () {
				vm.search_items = null;
				var params = $location.search();
				vm.alert = (angular.isDefined(params.alert) && params.alert !== '') ? parseInt(params.alert, 10) : '';
				vm.reset();
			});

			vm.search = function (clickedSearch) {

				if (vm.search_params.manufacturer) {
					vm.search_params.manf_id = vm.search_params.manufacturer.id;
				}

				var dataParams = angular.extend(vm.search_params, {
					alerts: true,
					alert: vm.alert
				});

				$http.get('/inventory/ajax/' + $scope.org_id + '/list/', {params: dataParams}).then(function (response) {

					var data = response.data;
					vm.search_items = data.items;
					vm.total_count = data.total_count;
					vm.alert_counts = data.alerts;
					vm.clicked_search = clickedSearch;
				});
			};

			vm.reset = function () {
				for (var key in vm.search_params) {
					delete vm.search_params[key];
				}
				vm.search_params.p = 0;
				vm.search_params.l = config.pagination.limit;
				vm.search();
			};

			vm.setAlert = function (type) {
				$location.search('alert', type);
			};

			vm.order = function() {
				vm.order_modal = $scope.dialog('inventory/order.html', $scope, {windowClass: 'alert'});
			};

			vm.orderQuery = function(ids) {
				$http.post('/orders/ajax/outgoing/' + $scope.org_id + '/save/', $.param({items: ids})).then(function (resp) {
					if (resp.data.id) {
						$scope.dialog('inventory/order_added.html', $scope, {windowClass: 'alert'}).result.then(function () {
							$window.location = '/orders/outgoing/' + $scope.org_id + '/view/' + resp.data.id;
						});
						vm.search();
					}
				});
			};

			vm.orderAll = function() {
				vm.order_modal.close();

				var ids = [];
				angular.forEach(vm.search_items, function (item) {
					ids.push(item.id);
				});
				vm.orderQuery(ids);
			};

			vm.orderSelected = function() {
				vm.orderQuery(vm.selection);
			};

			vm.orderSelection = function() {
				vm.order_modal.close();
				vm.selection = [];
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

			vm.getInventoryTypeOptions = function() {
				return Source.getData('/inventory/ajax/' + $scope.org_id + '/types', {
					alert: vm.alert
				});
			};

			vm.getManufacturerOptions = function(query) {
				return Source.getData('/inventory/ajax/' + $scope.org_id + '/manufacturers', {
					query: query,
					alert: vm.alert
				});
			};

		}]);

})(opakeApp, angular);
