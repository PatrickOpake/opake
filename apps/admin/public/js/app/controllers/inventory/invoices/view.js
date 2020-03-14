// Inventory Invoice Form
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('InventoryInvoiceCrtl', [
		'$scope',
		'$http',
		'$window',
		'$filter',
		'InventoryInvoice',
		function ($scope, $http, $window, $filter, InventoryInvoice) {
			var vm = this;

			vm.loaded = false;
			vm.errors = null;

			vm.init = function (id) {
				$http.get('/inventory/invoices/ajax/' + $scope.org_id + '/invoice/' + id).then(function (result) {
					vm.form = new InventoryInvoice(result.data);
					vm.loaded = true;
				});
			};

			vm.getPreviewUrl = function () {
				return '/inventory/invoices/ajax/pdf/' + $scope.org_id + '/generatePreviewImage/' + vm.form.id;
			};

			vm.save = function () {
				vm.errors = null;
				$http.post('/inventory/invoices/ajax/' + $scope.org_id + '/update/' + vm.form.id, $.param({data: JSON.stringify(vm.form)})).then(function (result) {
					if (result.data.success) {
						$window.location = '/inventory/invoices/' + $scope.org_id;
					} else {
						vm.errors = result.data.errors;
					}
				});
			};

			vm.addItem = function () {
				if (vm.addingItem && vm.addingItem.id && !$filter('filter')(vm.form.items, {id: vm.addingItem.id}).length) {
					vm.form.items.push(vm.addingItem);
				}
				vm.addingItem = {};
			};

			vm.removeItem = function (item) {
				var idx = vm.form.items.indexOf(item);
				if (idx > -1) {
					vm.form.items.splice(idx, 1);
				}
			};
		}]);

})(opakeApp, angular);
