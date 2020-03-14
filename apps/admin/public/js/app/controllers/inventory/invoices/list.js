// Inventory Invoice list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('InventoryInvoiceListCrtl', [
		'$scope',
		'$http',
		'$controller',
		'$window',
		'View',
		'InventoryInvoice',
		function ($scope, $http, $controller, $window, View, InventoryInvoice) {

			var vm = this;
			vm.isShowLoading = false;

			$controller('ListCrtl', {vm: vm});

			vm.init = function (inventoryId) {
				if (inventoryId) {
					vm.inventoryId = inventoryId;
				}
				vm.search();
			};

			vm.search = function () {
				vm.isShowLoading = true;
				var requestParams = prepareFilterParams();
				$http.get('/inventory/invoices/ajax/' + $scope.org_id, {params: requestParams}).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (val) {
						items.push(new InventoryInvoice(val));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
					vm.isShowLoading = false;
				});
			};

			vm.openUploadDialog = function () {
				vm.modalErrors = null;
				vm.form = new InventoryInvoice();
				vm.modal = $scope.dialog('inventory/invoices/upload-form.html', $scope, {windowClass: 'inventory-invoice--modal'});
			};

			vm.uploadFile = function (file) {
				vm.form.uploadedFile = file[0];
				$scope.$apply();
			};

			vm.removeUploadedFile = function () {
				vm.form.uploadedFile = null;
			};

			vm.deleteInvoice = function (invoiceId) {
				$scope.dialog(
					'inventory/invoices/delete-item-modal.html',
					$scope,
					{windowClass: 'inventory-invoice--modal'}
				).result.then(function() {
						$http.post('/inventory/invoices/ajax/' + $scope.org_id + '/delete/' + invoiceId).then(function (result) {
							if (result.data.success) {
								vm.search();
							}
						});
				});
			};

			vm.clickUpload = function () {
				vm.isUploadLoading = true;
				if (this.isFormValid()) {
					var fd = new FormData();
					fd.append('data', JSON.stringify(vm.form));
					fd.append('uploadedFile', vm.form.uploadedFile);

					$http.post('/inventory/invoices/ajax/' + $scope.org_id + '/create/', fd, {
						withCredentials: true,
						headers: {'Content-Type': undefined},
						transformRequest: angular.identity
					}).then(function (result) {
						if (result.data.success) {
							vm.search();
							vm.modal.close();
							if (result.data.id) {
								$window.location = '/inventory/invoices/' + $scope.org_id + '/view/' + result.data.id;
							}
						} else {
							vm.modalErrors = result.data.errors;
						}
						vm.isUploadLoading = false;
					});
				}
			};

			vm.isFormValid = function () {
				return vm.form &&
					vm.form.name &&
					vm.form.date &&
					vm.form.manufacturers &&
					vm.form.uploadedFile;
			};

			vm.showPreview = function (item) {
				vm.previewItem = item;
				vm.modal = $scope.dialog(View.get('inventory/invoice_preview.html'), $scope, {windowClass: 'inventory-invoice--modal'});
			};

			vm.getPreviewUrl = function () {
				return '/inventory/invoices/ajax/pdf/' + $scope.org_id + '/generatePreviewImage/' + vm.previewItem.id;
			};

			function prepareFilterParams() {
				var requestParams = angular.copy(vm.search_params);
				if (requestParams.invoice) {
					requestParams.invoice = requestParams.invoice.id;
				}
				if (requestParams.manufacturer) {
					requestParams.manufacturer = requestParams.manufacturer.id;
				}
				if (vm.inventoryId) {
					requestParams.item = vm.inventoryId;
				} else if (requestParams.item) {
					requestParams.item = requestParams.item.id;
				}
				return requestParams;
			}

		}]);

})(opakeApp, angular);
