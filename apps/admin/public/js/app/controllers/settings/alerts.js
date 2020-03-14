(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('AlertsSettingCtrl', [
		'$scope',
		'$http',
		'$q',

		function ($scope, $http, $q) {

			var vm = this;
			vm.isShowLoading = false;

			vm.init = function(siteId) {
				vm.siteId = siteId;
				$http.get('/settings/alerts/ajax/' + $scope.org_id + '/index/' + siteId ).then(function (result) {
					vm.site = result.data.site;
				});
			};

			vm.save = function() {
				var def = $q.defer();
				vm.site.alert.site_id = vm.siteId;
				vm.isShowLoading = true;
				$http.post(
					'/settings/alerts/ajax/' + $scope.org_id + '/save/',
					$.param({data: JSON.stringify(vm.site.alert)})
				).then(function (result) {
					vm.errors = null;
					if (result.data.id) {
						vm.init(vm.siteId);
						vm.isShowLoading = false;
						def.resolve();
					} else if (result.data.errors) {
						vm.errors = result.data.errors.split(';');
						def.reject();
					}
				});

				return def.promise;
			};



		}]);

})(opakeApp, angular);
