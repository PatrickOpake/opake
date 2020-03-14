(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('SettingsHCPCYearsListCtrl', [
		'$scope',
		'$http',
		'$controller',
		'$filter',
		'View',
		function ($scope, $http, $controller, $filter, View) {

			var vm = this;

			$controller('ListCrtl', {vm: vm});

			vm.isFileUploading = false;
			vm.newDb = {};
			vm.newDb.year_id = null;
			vm.errors = null;
			
			var currentDate = new Date();
			var endYear = parseInt(currentDate.getFullYear()) + 4;
			vm.availableYears = $filter('range')([], 2016, endYear).map(String);
			vm.availableYears.sort(function(a, b){return b-a});

			vm.search = function () {
				$http.get('/settings/databases/hcpc/ajax', {params: vm.search_params}).then(function (response) {
					vm.items = response.data.items;
					vm.total_count = response.data.total_count;
				});
			};
			vm.search();

			vm.openUploadDocumentModal = function() {
				vm.modal = $scope.dialog(View.get('/settings/databases/hcpc/upload_modal.html'), $scope, {windowClass: 'alert forms upload'});
				vm.modal.result.then(function () {
					
				});
			};

			vm.uploadFile = function (file) {
				vm.newDb.file = file[0];
				$scope.$apply();
			};

			vm.uploadFormFileChanged = function(files) {
				vm.newDb.file = files[0];
				$scope.$apply();
			};

			vm.removeUploadedFile = function() {
				vm.newDb.file = null;
			};

			vm.uploadNewDb = function() {
				vm.newDb.year_id = null;
				$http.get('/settings/databases/hcpc/ajax/hasDbForYear/' + vm.newDb.year).then(function (response) {
					if (response.data.yearId) {
						$scope.dialog(View.get('/settings/databases/hcpc/db_for_year_overwrite.html'), $scope, {windowClass: 'alert'}).result.then(function () {
							vm.newDb.year_id = response.data.yearId;
							uploadDb();
						});
					} else {
						uploadDb();
					}
				});
			};

			function uploadDb() {
				if (!vm.isFileUploading) {
					vm.isFileUploading = true;

					var fd = new FormData();
					angular.forEach(vm.newDb, function (value, key) {
						fd.append(key, value);
					});

					return $http.post('/settings/databases/hcpc/ajax/uploadNewDb', fd, {
						withCredentials: true,
						headers: {'Content-Type': undefined},
						transformRequest: angular.identity
					}).then(function (result) {
						if (result.data.errors) {
							vm.errors = [result.data.errors];
						} else {
							vm.modal.close();
							vm.search();
						}
					}).finally(function () {
						vm.isFileUploading = false;
					});
				}
			};

		}]);

})(opakeApp, angular);
