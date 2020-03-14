// Form  docs
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('FormCustomCrtl', [
		'$scope',
		'$location',
		'$http',
		'$window',
		function ($scope, $location, $http, $window) {
			var vm = this;

			var PORTRAIT_WIDTH = 911,
				LANDSCAPE_WIDTH = 1284;

			vm.editorOptions = {
				width: PORTRAIT_WIDTH
			};

			vm.previewUrl = '/settings/forms/charts/ajax/custom/' + $scope.org_id + '/preview/';

			vm.init = function (id) {
				if (id) {
					$http.get('/settings/forms/charts/ajax/custom/' + $scope.org_id + '/form/' + id).then(function (result) {
						vm.form = result.data;
						vm.updateWidth();
					});
				} else {
					var params = $location.search();
					vm.form = {
						segment: params.segment ? params.segment : 'intake',
						name: '',
						own_text: ''
					};
					vm.updateWidth();
				}
			};

			vm.createDocument = function () {
				$http.post('/settings/forms/charts/ajax/custom/' + $scope.org_id + '/save/', $.param({data: JSON.stringify(vm.form)})).then(function (result) {
					if (result.data.id) {
						$window.location = '/settings/forms/charts/' + $scope.org_id;
					} else {
						vm.errors = result.data.errors.split(';');
					}
				});
			};

			vm.updateWidth = function () {
				if (vm.form && vm.form.is_landscape) {
					vm.editorOptions.width = LANDSCAPE_WIDTH;
				} else {
					vm.editorOptions.width = PORTRAIT_WIDTH;
				}
			};
		}]);

})(opakeApp, angular);
