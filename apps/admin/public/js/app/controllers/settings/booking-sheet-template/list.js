(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BookingSheetTemplateListCtrl', [
		'$scope',
		'$http',
		'$q',
		'$controller',
		function ($scope, $http, $q, $controller) {

			var vm = this;
			vm.templates = [];
			vm.isLoading = true;
			vm.sitesCount = 0;

			var getSitesCount = function () {
				$http.get('/clients/ajax/site/', {params: {org: $scope.org_id}}).then(function (result) {
					vm.sitesCount = result.data.length;
				});
			};

			getSitesCount();

			vm.init = function() {
				vm.isLoading = true;
				$http.get('/settings/booking-sheet-templates/ajax/' + $scope.org_id + '/list/').then(function (response) {
					vm.templates = response.data;
				}).finally(function() {
					vm.isLoading = false;
				});
			};

			vm.isAllSitesChecked = function (template) {
				return template.is_all_sites || template.sites.length == vm.sitesCount;
			};

			vm.isNoOneSitesChecked = function (template) {
				return !template.is_all_sites && !template.sites.length;
			};

			vm.renameTemplate = function(template) {
				$scope.dialog('booking-sheet-template/rename-template.html', $scope, {
					windowClass: 'alert booking-sheet-template-modal',
					controller: [
						'$scope',
						'$uibModalInstance',
						function ($scope, $uibModalInstance) {

							var modalVm = this;
							modalVm.template = angular.copy(template);
							modalVm.errors = null;

							modalVm.rename = function() {
								$http.post('/settings/booking-sheet-templates/ajax/' + $scope.org_id + '/update/', $.param({
									type: 'rename',
									data: angular.toJson(modalVm.template, false)
								})).then(function(result) {
									if (result.data.success) {
										vm.init();
										$uibModalInstance.dismiss('ok');
									} else {
										modalVm.errors = result.data.errors;
									}
								})
							};

							modalVm.close = function() {
								$uibModalInstance.dismiss('close');
							};
						}
					],
					controllerAs: 'modalVm'
				});
			};

			vm.assignTemplate = function(template) {
				$scope.dialog('booking-sheet-template/assign-template.html', $scope, {
					windowClass: 'booking-sheet-template-modal assign-modal',
					size: 'lg',
					controller: [
						'$scope',
						'$uibModalInstance',
						function ($scope, $uibModalInstance) {

							var modalVm = this;
							modalVm.template = angular.copy(template);
							modalVm.errors = null;

							modalVm.save = function() {
								$http.post('/settings/booking-sheet-templates/ajax/' + $scope.org_id + '/update/', $.param({
									type: 'assign',
									data: angular.toJson(modalVm.template, false)
								})).then(function(result) {
									if (result.data.success) {
										vm.init();
										$uibModalInstance.dismiss('ok');
									} else {
										modalVm.errors = result.data.errors;
									}
								})
							};

							modalVm.close = function() {
								$uibModalInstance.dismiss('close');
							};
						}
					],
					controllerAs: 'modalVm'
				});
			};

			vm.deleteTemplate = function(template) {
				$scope.dialog('booking-sheet-template/delete-template.html', $scope, {
					windowClass: 'alert booking-sheet-template-modal',
					controller: [
						'$scope',
						'$uibModalInstance',
						function ($scope, $uibModalInstance) {

							var modalVm = this;

							modalVm.delete = function() {
								vm.isLoading = true;
								$http.post('/settings/booking-sheet-templates/ajax/' + $scope.org_id + '/delete/' + template.id).then(
									function(result) {
										vm.isLoading = false;
										if (result.data.success) {
											vm.init();
											$uibModalInstance.dismiss('ok');
										}
									}
								)
							};

							modalVm.close = function() {
								$uibModalInstance.dismiss('close');
							};
						}
					],
					controllerAs: 'modalVm'
				});
			};

		}]);

})(opakeApp, angular);
