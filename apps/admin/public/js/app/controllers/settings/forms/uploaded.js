// Uploaded Form
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('FormUploadedCrtl', [
		'$scope',
		'$http',
		'$window',
		'Tools',
		'FormDocumentsConst',
		function ($scope, $http, $window, Tools, FormDocumentsConst) {
			var vm = this;

			vm.loaded = false;
			vm.errors = null;
			vm.options = FormDocumentsConst.DYNAMIC_OPTIONS;

			vm.init = function (id) {
				$http.get('/settings/forms/charts/ajax/uploaded/' + $scope.org_id + '/form/' + id).then(function (result) {
					vm.form = result.data;
					vm.loaded = true;
				});
			};

			vm.getPreviewUrl = function () {
				return '/settings/forms/charts/ajax/pdf/' + $scope.org_id + '/generatePreviewImage/' + vm.form.id;
			};

			vm.save = function () {
				vm.errors = null;
				$http.post('/settings/forms/charts/ajax/uploaded/' + $scope.org_id + '/save/' + vm.form.id, $.param({data: JSON.stringify(vm.form)})).then(function (result) {
					if (result.data.success) {
						$window.location = '/settings/forms/charts/' + $scope.org_id;
					} else {
						vm.errors = result.data.errors;
					}
				});
			};

			vm.preview = function () {
				Tools.windowOpenInPost('/settings/forms/charts/ajax/uploaded/' + $scope.org_id + '/preview/' + vm.form.id, JSON.stringify(vm.form));
			};

		}]);

})(opakeApp, angular);
