(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('InventoryMultiplierCtrl', [
		'$scope',
		'$http',
		'$window',
		'$location',
		'config',
		'InventoryMultiplier',

		function ($scope, $http, $window, $location, config, InventoryMultiplier) {

			var vm = this;
			vm.newItem = new InventoryMultiplier({type: 0});
			vm.search_params = {p: 0, l: config.pagination.limit};

			vm.init = function () {
				$http.get('/organizations/ajax/' + $scope.org_id + '/getChargeable/').then(function (response) {
					vm.charge_price = vm.original_charge_price = response.data.charge_price;
				});
			};

			vm.saveChargeable = function () {
				var data = {charge_price: vm.charge_price};
				$http.post('/organizations/ajax/' + $scope.org_id + '/saveChargeable/', $.param({data: JSON.stringify(data)})).then(function (result) {
					if (result.data.id) {
						vm.original_charge_price = vm.charge_price;
					}
				});
			};

			vm.search = function () {
				$http.get('/inventory-multiplier/ajax/' + $scope.org_id + '/list/', {params: angular.extend(vm.search_params)}).then(function (response) {
					var data = response.data;
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new InventoryMultiplier(data));
					});
					vm.multipliers = items;
					vm.total_count = data.total_count;
				});
			};

			vm.search();

			vm.editItem = function (item) {
				item.edit_mode = true;
				if (item.typeIsItemName()) {
					item.originat_inventory = item.inventory;
				} else if (item.typeIsItemType()) {
					item.originat_inventory_type = item.inventory_type;
				}
				item.original_multiplier = item.multiplier;
			};

			vm.cancelEditItem = function (item) {
				vm.errors = null;
				if (item.original_multiplier) {
					item.edit_mode = false;
					if (item.typeIsItemName()) {
						item.inventory = item.originat_inventory;
					} else if (item.typeIsItemType()) {
						item.inventory_type = item.originat_inventory_type;
					}
					item.multiplier = item.original_multiplier;
				} else {
					vm.removeItem(item);
				}
			};

			vm.saveItem = function (item) {
				vm.errors = null;
				$http.post('/inventory-multiplier/ajax/' + $scope.org_id + '/save/', $.param({
					data: JSON.stringify(item)
				})).then(function (result) {
					if (result.data.id) {
						item.edit_mode = false;
					} else if (result.data.errors) {
						vm.errors = [result.data.errors];
					}
				});
			};

			vm.removeItem = function (item) {
				var idx = vm.multipliers.indexOf(item);
				if (idx > -1) {
					vm.multipliers.splice(idx, 1);
				}
				$http.get('/inventory-multiplier/ajax/' + $scope.org_id + '/remove/' + item.id);
			};

			vm.AddNewMultiplier = function (item) {
				vm.errors = null;
				$http.post('/inventory-multiplier/ajax/' + $scope.org_id + '/save/', $.param({
					data: JSON.stringify(item)
				})).then(function (result) {
					if (result.data.id) {
						vm.newItem = new InventoryMultiplier({type: 0});
						vm.search();
					} else if (result.data.errors) {
						vm.errors = [result.data.errors];
					}
				});
			};

			vm.canSaveItem = function (item) {
				var result = true;
				if ((!item.multiplier)
				|| (item.typeIsItemName() && !item.inventory)
				|| (item.typeIsItemType() && !item.inventory_type)) {
					result = false;
				}

				return result;
			};

		}]);

})(opakeApp, angular);
