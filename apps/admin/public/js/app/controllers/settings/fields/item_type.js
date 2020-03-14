(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('SettingItemTypeCrtl', [
		'$scope',
		'$http',
		'$controller',
		'$window',
		function ($scope, $http, $controller, $window) {

			var vm = this;
			vm.errors = [];

			vm.reupload = function (files, itemId) {

				var form = {file: files[0], 'item_id': itemId};

				var fd = new FormData();

				angular.forEach(form, function(value, key) {
					fd.append(key, value);
				});

				$http.post('/settings/fields/ajax/reuploadImage/', fd, {
					withCredentials: true,
					headers: {'Content-Type': undefined},
					transformRequest: angular.identity
				}).then(function (result) {
					if (result.data.success) {
						$window.location.reload();
					} else {
						vm.errors = result.data.error;
						console.log(vm.errors);
					}
				});
			};

		}]);

})(opakeApp, angular);
