// Outgoing Order Adding
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('OrderReceivedAddingCrtl', [
		'$scope',
		'$http',
		'$window',
		'config',
		'OrderReceived',
		'BeforeUnload',
		function ($scope, $http, $window, config, OrderReceived, BeforeUnload) {

			var vm = this;

			vm.errors = [];
			vm.search_items = [];
			vm.selection = [];
			vm.total_count = 0;
			vm.step = 1;
			vm.order = new OrderReceived({items:[], status: 2});
			vm.search_params = {p: 0, l: config.pagination.limit};

			vm.search = function () {
				$http.get('/inventory/ajax/' + $scope.org_id + '/list/', {params: angular.extend(vm.search_params, {org_id: $scope.org_id})}).then(function (response) {
					var data = response.data;
					vm.search_items = data.items;
					vm.total_count = data.total_count;
				});
			};

			vm.getInventories = function() {
				$http.post('/orders/ajax/received/' + $scope.org_id + '/inventoryList', $.param({items: vm.selection})).then(function (response) {
					var data = response.data;
					vm.order.items = [];
					angular.forEach(data.inventories, function (inventory) {
						var item = vm.getMasterItem();
						item.inventory = inventory;
						item.inventory_id = inventory.id;
						vm.order.items.push(item);
					});
				});
			};

			vm.reset = function () {
				for (var key in vm.search_params) {
					if (key != 'vendor') {
						delete vm.search_params[key];
					}
				}
				vm.search_params.p = 0;
				vm.search_params.l = config.pagination.limit;
				vm.search();
			};

			vm.save = function () {
				$http.post('/orders/ajax/received/' + $scope.org_id + '/saveOutsideOrder/', $.param({order: vm.order.toOutsideJson()})).then(function (resp) {
					if (resp.data.id) {
						BeforeUnload.reset(true);
						$window.location = '/orders/' + $scope.org_id + '/view/' + resp.data.id;
					} else if (resp.data.errors) {
						vm.errors = resp.data.errors.split(';');
					}
				});
			};

			vm.delete = function (item) {
				var items = vm.order.items;
				if (confirm('Are you sure?')) {
					var idx = items.indexOf(item);
					if (idx > -1) {
						items.splice(idx, 1);
						vm.toggleSelection(item.id);
					}
				}
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

			vm.setStep = function(step){
				vm.step = step;
			};

			vm.changeVendor = function(){
				if (vm.step < 3) {
					vm.resetSelection();
					vm.search_params.vendor = vm.order.vendor.id;
					vm.search();
					vm.setStep(2);
				}
			};

			vm.addItems = function() {
				if(vm.step === 2) {
					vm.getInventories();
					vm.setStep(3);
				}
			};

			vm.getMasterItem = function() {
				return {received: 0, location_id: 0, status: 2 };
			};
		}]);

})(opakeApp, angular);
