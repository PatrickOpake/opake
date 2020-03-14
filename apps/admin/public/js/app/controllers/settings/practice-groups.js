(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PracticeGroupsCtrl', [
		'$scope',
		'$http',
		'$controller',
		'View',
		function ($scope, $http, $controller, View) {

			var vm = this;
			vm.searchParams = {};
			vm.items = [];
			vm.totalCount = 0;

			$controller('ListCrtl', {vm: vm});

			vm.search = function () {
				var data = vm.searchParams;

				$http.get('/settings/practice-groups/ajax/index/', {params: data}).then(function (response) {
					vm.items = response.data.items;
					vm.totalCount = response.data.total_count;
				});
			};

			vm.reset = function () {
				vm.searchParams = {
					p: 0,
					l: 20
				};
				vm.search();
			};

			vm.openCreateDialog = function() {
				vm.openForm({
					name: ''
				});
			};

			vm.openEditDialog = function(group) {
				vm.openForm(group);
			};

			vm.openForm = function(group) {
				var isNew = !group.id;
				$scope.dialog('settings/practice-groups/form.html', $scope,  {
					size: 'md',
					controller: [
						'$scope',
						'$uibModalInstance',
						function($scope, $uibModalInstance) {
							var modalVm = this;
							modalVm.errors = [];
							modalVm.group = group;
							modalVm.isCreate = isNew;

							modalVm.save = function() {
								$http.post('/settings/practice-groups/ajax/save/', $.param({data: JSON.stringify(modalVm.group)})).then(function (result) {
									if (result.data.success) {
										vm.search();
										$uibModalInstance.dismiss('ok');
									} else if (result.data.errors) {
										modalVm.errors =  result.data.errors;
									}
								});
							};

							modalVm.cancel = function() {
								$uibModalInstance.dismiss('cancel');
							};
						}
					],
					controllerAs: 'modalVm'
				});
			};

			vm.activate = function(group) {
				$scope.dialog('settings/practice-groups/confirm-activate-modal.html', $scope).result.then(function () {
					$http.get('/settings/practice-groups/ajax/activate/' + group.id).then(function () {
						vm.search();
					});
				});
			};

			vm.deactivate = function(group) {
				$scope.dialog('settings/practice-groups/confirm-deactivate-modal.html', $scope).result.then(function () {
					$http.get('/settings/practice-groups/ajax/deactivate/' + group.id).then(function () {
						vm.search();
					});
				});
			};

			vm.delete = function(group) {
				$scope.dialog('settings/practice-groups/confirm-delete-modal.html', $scope).result.then(function () {
					$http.get('/settings/practice-groups/ajax/delete/' + group.id).then(function () {
						vm.search();
					});
				});
			};


			vm.reset();

		}]);

})(opakeApp, angular);
