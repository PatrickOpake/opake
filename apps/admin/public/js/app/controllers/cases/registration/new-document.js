(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseRegistrationNewDocumentCtrl', [
		'$scope',
		'$http',

		function ($scope, $http) {

			var vm = this;

			vm.doc = {};
			vm.isFileUploading = false;
			vm.errors = null;

			vm.uploadFile = function (file) {
				vm.doc.file = file[0];
				$scope.$apply();
			};

			vm.uploadFormFileChanged = function(files) {
				vm.doc.file = files[0];
				$scope.$apply();
			};

			vm.removeUploadedFile = function() {
				//TODO: Перенести это в директиву
				var input_file = angular.element('.file-drop-box input[type="file"]');
				input_file.replaceWith(input_file.val('').clone(true));

				vm.doc.file = null;
			};

			vm.uploadNewDocument = function() {
				if (!vm.isFileUploading) {
					vm.isFileUploading = true;

					var fd = new FormData();
					angular.forEach(vm.doc, function(value, key) {
						fd.append(key, value);
					});

					var caseId;
					if (vm.reg && vm.reg.case_id) {
						caseId = vm.reg.case_id;
					} else {
						caseId = $scope.regVm.registration.case_id;
					}

					return $http.post('/cases/registrations/ajax/' + $scope.org_id + '/uploadNewType/' + caseId, fd, {
						withCredentials: true,
						headers: {'Content-Type': undefined},
						transformRequest: angular.identity
					}).then(function (result) {
						if (result.data.error) {
							vm.errors = [result.data.error];
						} else {
							$scope.ok();
						}
					}).finally(function() {
						vm.isFileUploading = false;
					});
				}
			};

		}]);

})(opakeApp, angular);
