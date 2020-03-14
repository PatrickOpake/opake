(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('InsurancePayorsListCtrl', [
		'$scope',
		'$http',
		'$controller',
		'$filter',
		'View',
		function ($scope, $http, $controller, $filter, View) {

			var vm = this;

			$controller('ListCrtl', {vm: vm});

			vm.items = [];
			vm.totalCount = null;
			vm.isLoading = false;
			vm.errors = null;
			vm.addressNumbers = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15];

			vm.search = function () {
				var data = vm.search_params;
				$http.get('/settings/databases/insurance-payors/ajax', { params: data }).then(function (response) {
					vm.items = response.data.items;
					angular.forEach(vm.items, function(item) {
						angular.forEach(vm.addressNumber, function (num) {
							if (!item.addresses[num - 1]) {
								item.addresses[num - 1] = {
									address: '',
									city_name: '',
									state_name: '',
									phone: '',
									zip_code: ''
								}
							}
						});
					});
					vm.totalCount = response.data.totalCount;
				});
			};

			vm.reset();

			vm.downloadDatabase = function () {
				window.location = '/settings/databases/insurance-payors/downloadInsuranceDB';
			};

			vm.uploadDatabase = function(files) {
				vm.isLoading = true;
				vm.errors = null;
				var insuranceDBFile = files[0];
				var fd = new FormData();
				fd.append('file', insuranceDBFile);
				$http.post('/settings/databases/insurance-payors/ajax/uploadInsurancesDB/', fd, {
					withCredentials: true,
					headers: {
						'Content-Type': undefined
					},
					transformRequest: angular.identity
				}).then(function (result) {
					if (result.data.errors) {
						vm.errors = result.data.errors;
					} else {
						vm.search();
					}
				}).finally(function() {
					vm.isLoading = false;
				});
			};


			vm.edit = function(itemId){
				vm.modal = $scope.dialog(View.get('settings/databases/insurance-payors/edit-modal.html'), $scope,  {
					size: 'lg',
					controller: 'InsurancePayorsEditCtrl',
					controllerAs: 'modalVm',
					resolve: {
						payorId: () => itemId
					}
				}).result.then(function() {
					vm.search();
				});
			};

			vm.addRow = function () {
				vm.edit(null);
			};

			vm.delete = function(itemId) {
				$scope.dialog(View.get('settings/databases/insurance-payors/confirm-delete.html'),
					$scope, {windowClass: 'alert'})
				.result.then(function() {
					$http.post('/settings/databases/insurance-payors/ajax/delete/', $.param({
						data: angular.toJson({
							id: itemId
						})
					})).then(function (res) {
						if (res.data.success) {
							$scope.$emit('flashAlertMessage', 'The insurance company has been successfully deleted');
							vm.search();
						} else {
							vm.errors = res.data.errors;
						}
					});
				});
			};

			vm.save = function() {
			};
		}]);

})(opakeApp, angular);
