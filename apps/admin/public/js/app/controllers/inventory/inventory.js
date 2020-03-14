(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('InventoryCrtl', [
		'$scope',
		'$http',
		'$q',
		'$filter',
		'$window',
		'View',
		'Inventory',
		'InventorySupply',
		'Source',

		function ($scope, $http, $q, $filter, $window, View, Inventory, InventorySupply, Source) {

			var vm = this;
			vm.isShowForm = false;
			vm.isShowQuantitiesForm = false;

			vm.errors = null;

			vm.inventoryStatuses = ['active', 'inactive'];

			vm.inventoryCodeTypes = {
				1: 'Barcode',
				0: 'Other'
			};

			vm.tabActivity = {details: true, purchasing: false};
			vm.showSubstitutions = false;
			vm.search_params = {};
			vm.search_items = [];

			vm.init = function(id) {
				if (id) {
					$http.get('/inventory/ajax/' + $scope.org_id + '/inventory/' + id).then(function (result) {
						vm.inventory = new Inventory(result.data);
					});
				} else {
					vm.inventory = new Inventory();
					vm.isShowForm = true;
				}
			};

			vm.edit = function() {
				vm.isShowForm = true;
				vm.originalInventory = angular.copy(vm.inventory);
			};

			vm.editQuantities = function() {
				vm.isShowQuantitiesForm = true;
				vm.originalInventory = angular.copy(vm.inventory);
			};

			vm.back = function() {
				$window.history.back();
			};

			vm.cancel = function() {
				if (vm.inventory.id) {
					vm.inventory = vm.originalInventory;
					vm.isShowForm = false;
					vm.errors = null;
				} else {
					history.back();
				}
			};

			vm.save = function() {
				var def = $q.defer();
				var isCreate = !vm.inventory.id;
				$http.post('/inventory/ajax/' + $scope.org_id + '/save/', $.param({data: JSON.stringify(vm.inventory)})).then(function (result) {
					vm.errors = null;
					if (result.data.id) {
						var savingDone = function() {
							if (isCreate) {
								window.location = '/inventory/' + $scope.org_id + '/view/' + result.data.id;
								def.resolve();
							} else {
								vm.isShowForm = false;
								vm.isShowQuantitiesForm = false;
								vm.init(result.data.id);
								def.resolve();
							}
						};

						savingDone();

					} else if (result.data.errors) {
						vm.errors = result.data.errors.split(';');
						def.reject();
					}
				});

				return def.promise;
			};

			vm.getView = function () {
				var view = 'inventory/' + (vm.isShowForm ? 'form' : 'view') + '.html';
				return View.get(view);
			};

			vm.delete = function () {
				var itemId = vm.inventory.id;
				$scope.dialog('inventory/delete-item-modal.html', $scope, {windowClass: 'alert'}).result.then(function () {
					$http.post('/inventory/ajax/' + $scope.org_id + '/delete/' + itemId).then(function () {
						window.location = '/inventory/' + $scope.org_id;
					});
				});
			};

			vm.deleteInventory = function (inventoryId) {
				$scope.dialog(View.get('inventory/confirm_delete.html'), $scope, {windowClass: 'alert'}).result.then(function () {
					$http.post('/inventory/ajax/' + $scope.org_id + '/delete/' + inventoryId).then(function () {
						window.location = '/inventory/' + $scope.org_id;
					});
				});
			};

			vm.addSupply = function() {
				vm.supply = new InventorySupply();

				vm.modal = $scope.dialog('opake/inventory/supply.html', $scope, {size: 'md'});
				vm.modal.result.then(function () {
					if (vm.supply) {
						vm.inventory.supplies.push(vm.supply);
						vm.supply = null;
					}
				});
			};

			vm.editSupply = function(supply) {
				vm.supply = supply;
				vm.modal = $scope.dialog('opake/inventory/supply.html', $scope, {size: 'md'});
				vm.modal.result.then(function () {
				});
			};

			vm.removeSupply = function(supply) {
				var index = vm.inventory.supplies.indexOf(supply);
				vm.inventory.supplies.splice(index, 1);
			};

			//Codes

			vm.addCode = function (code) {
				vm.inventory.codes.push(code);
			};

			$scope.getCodeMaster = function () {
				return {type: '1', code: ''};
			};

			//Packs

			vm.getPackMaster = function () {
				return {quantity: '', site: '', location: '', exp_date: ''};
			};

			vm.addPack = function (pack) {
				vm.inventory.packs.push(pack);
			};

			vm.removePack = function (pack, tmpl) {
				$scope.dialog(tmpl, $scope, {windowClass: 'alert'}).result.then(function () {
					vm.inventory.packs.splice(vm.inventory.packs.indexOf(pack), 1);
				});
			};

			vm.locationChange = function (pack) {
				if (!pack.site || (pack.location && pack.site.id !== pack.location.site_id)) {
					angular.forEach(Source.getSites(), function(item){
						if (item.id === pack.location.site_id) {
							pack.site = item;
						}
					});
				}
			};
			
			vm.changeSubstitutionsShow = function() {
				vm.showSubstitutions = !vm.showSubstitutions;
			};

			vm.removeImage = function(){
				vm.inventory.image = null;
				vm.inventory.image_id = null;
			};

			vm.search = function () {
				return $http.get('/master/ajax/' + $scope.org_id , {params: angular.extend(vm.search_params, {l: 5})}).then(function (response) {
					var results = response.data.items;
					angular.forEach(results, function(item){
						if ($filter('filter')(vm.inventory.substitutes, {id: item.id }).length) {
							item.disable = true;
						}
					});
					vm.search_items = results;
				});
			};

			vm.checkSubstitutionsItem = function (item, exist) {
				var check = true;
				if (item.id) {
					check = !$filter('filter')(vm.inventory.substitutes, {id: item.id, }).length;
				}
				return check;
			};


			vm.reset = function () {
				angular.forEach(vm.search_params, function (val, key) {
					vm.search_params[key] = "";
				});
				vm.search();
			};

			vm.addSubstitutions = function() {
				vm.search();
				$scope.dialog(View.get('inventory/add_substitutions.html'), $scope, {size: 'lg'}).result.then(function () {

				});
			};

			vm.addSubstitutionsItem = function (item) {
				vm.inventory.substitutes.push(item);
			};

			this.getAllInventoryTypes = function () {
				return Source.getData('/inventory/ajax/' + $scope.org_id + '/allTypes');
			};
		}]);

})(opakeApp, angular);
