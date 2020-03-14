// KitItemsCrtl
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('KitItemsCrtl', [
		'$scope',
		'$http',
		'$filter',
		'View',

		function ($scope, $http, $filter, View) {

			var vm = angular.isDefined($scope.vm) ? $scope.vm : this;

			vm.items = [];
			vm.item_error = '';
			vm.search_params = {};
			vm.search_items = [];
			vm.new_item;

			vm.init = function (items) {
				vm.items = items;
			};

			vm.getItemMaster = function () {
				return {inventory: {id: '', name: ''}, quantity: null};
			};

			vm.checkItem = function (item, exist) {
				var check = true;
				if (item.inventory_id && !exist) {
					check = !$filter('filter')(vm.items, {item_id: item.inventory_id, }).length;
				}
				if (item.id) {
					check = !$filter('filter')(vm.items, {item_id: item.id, }).length;
				}
				return check;
			};

			vm.search = function () {
				return $http.get('/cards/ajax/' + $scope.org_id + '/search/', {params: angular.extend(vm.search_params, {l: 5})}).then(function (response) {
					var results = response.data;
					angular.forEach(results, function(item){
						if ($filter('filter')(vm.items, {inventory_id: item.id, }).length) {
							item.disable = true;
						}
					});
					vm.search_items = results;
				});
			};
			vm.reset = function () {
				angular.forEach(vm.search_params, function (val, key) {
					vm.search_params[key] = "";
				});
				vm.search();
			};
			vm.addItemDialog = function () {
				vm.search();
				$scope.dialog(View.get('cards/blocks/item_add.html'), $scope, {size: 'lg'}).result.then(function () {
					
				});
			};
			vm.addItem = function (item) {
				vm.new_item = vm.getItemMaster();
				vm.new_item.item_id = item.id;
				vm.new_item.inventory = item;

				$scope.dialog(View.get('cards/blocks/item_add_quantity.html'), $scope, {size: 'sm'}).result.then(function () {
					vm.items.push(vm.new_item);
					$scope.$emit('addItem');
				});
			};

			vm.removeItem = function (item, tmpl) {
				$scope.dialog(tmpl, $scope, {windowClass: 'alert'}).result.then(function () {
					vm.items.splice(vm.items.indexOf(item), 1);
				});
			};
		}]);

})(opakeApp, angular);
