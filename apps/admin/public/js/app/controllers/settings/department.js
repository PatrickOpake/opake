(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('DepartmentCrtl', [
		'$scope',
		'$http',
		'$controller',
		'$filter',
		'Department',
		function ($scope, $http, $controller, $filter, Department) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.department = null;

			vm.init = function (id) {
				if (id) {
					$http.get('/settings/departments/ajax/department/' + id).then(function (result) {
						vm.department = new Department(result.data);
					});
				} else {
					vm.department = new Department();
				}
			};

			vm.search = function () {
				var data = vm.search_params;

				$http.get('/settings/departments/ajax/index/', {params: data }).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new Department(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
				});
			};
			vm.search();

			vm.openCreateDialog = function() {
				vm.init();
				vm.modal = $scope.dialog('settings/departments/create.html', $scope,  {size: 'md'});
				vm.modal.result.then(function () {
					vm.save(vm.department, function(result) {
						if (result.data.id) {
							vm.init(result.data.id);
								vm.errors = [];
						} else if (result.data.errors) {
							vm.errors =  result.data.errors.split(';');
						}
					});
				});
				vm.search();
			};

			vm.openEditDialog = function(department) {
				vm.init(department.id);
				vm.modal = $scope.dialog('settings/departments/edit.html', $scope,  {size: 'md'});
				vm.modal.result.then(function () {
					if (vm.department) {
						vm.save(vm.department, function (result) {
							if (result.data.id) {
								vm.init(result.data.id);
								vm.errors = [];
							} else if (result.data.errors) {
								vm.errors =  result.data.errors.split(';');
							}
						});
					}
				});
				vm.search();
			};

			vm.activate = function(departmentId) {
				$scope.dialog('settings/departments/confirm_activate.html', $scope).result.then(function () {
					$http.get('/settings/departments/ajax/activate/' + departmentId).then(function () {
						vm.search();
					});
				});
			};

			vm.deactivate = function(departmentId) {
				$scope.dialog('settings/departments/confirm_deactivate.html', $scope).result.then(function () {
					$http.get('/settings/departments/ajax/deactivate/' + departmentId).then(function () {
						vm.search();
					});
				});
			};

			vm.delete = function(departmentId) {
				$scope.dialog('settings/departments/confirm_delete.html', $scope).result.then(function () {
					$http.get('/settings/departments/ajax/delete/' + departmentId).then(function () {
						vm.search();
					});
				});
			};

			vm.cancel = function() {
				vm.department = null;
				vm.modal.close();
			};

			vm.clickSave = function(departmentForm) {
				if (departmentForm.$valid) {
					vm.modal.close();
				}
			};

			vm.save = function (data, callback) {
				$http.post('/settings/departments/ajax/save/', $.param({data: JSON.stringify(data)})).then(function (result) {
					callback(result);
				});

				vm.search();
				vm.department = null;
			};

		}]);

})(opakeApp, angular);
