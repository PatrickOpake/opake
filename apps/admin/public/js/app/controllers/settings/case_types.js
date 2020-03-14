(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseTypeListCrtl', [
		'$scope',
		'$http',
		'$controller',
		'$filter',
		'View',
		'Tools',
		'CaseType',
		'ProcedureConst',
		function ($scope, $http, $controller, $filter, View, Tools, CaseType, ProcedureConst) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.caseType = null;
			vm.procedureConst = ProcedureConst;
			vm.isExportGenerating = false;

			vm.init = function (id) {
				if (id) {
					$http.get('/settings/case-types/ajax/' + $scope.org_id + '/caseType/' + id).then(function (result) {
						vm.caseType = new CaseType(result.data);
					});
				} else {
					vm.caseType = new CaseType();
				}
			};

			vm.search = function () {
				var data = vm.search_params;
				
				if (data.cpt) {
					data.cpt_name = data.cpt.name;
				}

				$http.get('/settings/case-types/ajax/' + $scope.org_id + '/index/', {params: data }).then(function (response) {
					var items = [];
					angular.forEach(response.data.items, function (data) {
						items.push(new CaseType(data));
					});
					vm.items = items;
					vm.total_count = response.data.total_count;
				});
			};
			vm.search();

			vm.export = function() {
				vm.isExportGenerating = true;
				$http.get('/settings/case-types/ajax/' + $scope.org_id + '/export/').then(function (response) {
					if (response.data.success) {
						Tools.export(response.data.url);
						vm.isExportGenerating = false;
					}
				});
			};

			vm.openCreateDialog = function() {
				vm.init();
				vm.caseType.is_active = true;
				vm.modal = $scope.dialog(View.get('settings/case-types/create.html'), $scope,  {size: 'md'});
				vm.modal.result.then(function () {
					vm.save(vm.caseType, function(result) {
						if (result.data.id) {
							vm.init(result.data.id);
								vm.errors = [];
						} else if (result.data.errors) {
							vm.errors =  result.data.errors.split(';');
						}
						vm.search();
					});
				});
			};

			vm.openEditDialog = function(caseType) {
				vm.init(caseType.id);
				vm.modal = $scope.dialog(View.get('settings/case-types/edit.html'), $scope,  {size: 'md'});
				vm.modal.result.then(function () {
					if (vm.caseType) {
						vm.save(vm.caseType, function (result) {
							if (result.data.id) {
								vm.init(result.data.id);
								vm.errors = [];
							} else if (result.data.errors) {
								vm.errors =  result.data.errors.split(';');
							}
						});
					}
					vm.search();
				});
			};

			vm.activate = function(caseTypeId) {
				$scope.dialog(View.get('settings/case-types/confirm_activate.html'), $scope).result.then(function () {
					$http.get('/settings/case-types/ajax/' + $scope.org_id + '/activate/' + caseTypeId).then(function () {
						vm.search();
					});
				});
			};

			vm.deactivate = function(caseTypeId) {
				$scope.dialog(View.get('settings/case-types/confirm_deactivate.html'), $scope).result.then(function () {
					$http.get('/settings/case-types/ajax/' + $scope.org_id + '/deactivate/' + caseTypeId).then(function () {
						vm.search();
					});
				});
			};

			vm.activateCaseType = function() {
				vm.caseType.is_active = true;
			};

			vm.deactivateCaseType = function() {
				vm.caseType.is_active = false;
			};

			vm.cancel = function() {
				vm.caseType = null;
				vm.modal.close();
			};

			vm.clickSave = function(caseTypeForm) {
				caseTypeForm.$pristine = false;
				if (caseTypeForm.$valid) {
					vm.modal.close();
				}
			};

			vm.save = function (data, callback) {
				data.active = data.is_active;
				$http.post('/settings/case-types/ajax/' + $scope.org_id + '/save/', $.param({data: JSON.stringify(data)})).then(function (result) {
					callback(result);
					vm.search();
				});
				vm.cpts = null;
			};

			vm.upload = function(files) {
				vm.isExportGenerating = true;
				vm.errors = null;
				var proceduresFile = files[0];
				var fd = new FormData();
				fd.append('file', proceduresFile);
				$http.post('/settings/case-types/ajax/' + $scope.org_id + '/upload/', fd, {
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
					vm.isExportGenerating = false;
				});
			};

		}]);

})(opakeApp, angular);
