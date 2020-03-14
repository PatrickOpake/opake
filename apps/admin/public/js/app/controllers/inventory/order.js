// Inventory order
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('InventoryOrderCrtl', [
		'$scope',
		'$controller',
		'$http',
		'View',
		'Vendor',
		'VendorConst',

		function ($scope, $controller, $http, View, Vendor, VendorConst) {
			$scope.vendorConst = VendorConst;

			var vm = this;
			$controller('ListCrtl', {vm: vm});
			var item_id;

			vm.vendors = [];
			vm.modal;

			vm.init = function(id) {
				item_id = id;
			};

			vm.order = function() {
				var q = vm.search({}).then(function () {
					if (vm.vendors.length > 1) {
						$scope.dialog(View.get('/inventory/order/vendors.html'), $scope, {windowClass: 'inventory-order', size: 'lg'});
					} else if (vm.vendors.length === 1) {
						vm.email(vm.vendors[0]);
					} else {
						vm.email();
					}
				});
			};

			vm.search = function () {
				var data = vm.search_params;
				return $http.get('/inventory/ajax/' + $scope.org_id + '/vendors/' + item_id, {params: angular.extend(data, {l: 5})}).then(function (response) {
					var vendors = [];
					angular.forEach(response.data, function(data){
						vendors.push(new Vendor(data));
					});
					vm.vendors = vendors;
				});
			};

			vm.getMasterEmail = function() {
				return {};
			};
			vm.email = function (vendor) {
				vm.mail = {};
				vm.errors = [];

				if (vendor) {
					vm.mail.to = vendor.email;
				}

				vm.modal = $scope.dialog(View.get('/inventory/order/mail.html'), $scope, {windowClass: 'inventory-order', size: 'md'});
			};

			vm.sendMail = function () {
				var conf = true;
				if (!vm.mail.subject && !vm.mail.body) {
					conf = confirm('Send this message without a subject and text in the body?');
				}
				if (conf) {
					vm.sending = true;
					$http.post('/ajax/email/', $.param({
						data: JSON.stringify(vm.mail)
					})).then(function (result) {
						if (result.data.errors) {
							vm.errors = result.data.errors.split(';');
						} else {
							vm.modal.close();
						}
						vm.sending = false;
					});
				}
			};

		}]);

})(opakeApp, angular);
