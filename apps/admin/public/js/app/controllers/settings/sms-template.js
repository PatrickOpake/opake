// SMS Template
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('SmsTemplateCtrl', [
		'$scope',
		'$http',
		'$q',
		function ($scope, $http, $q) {

			var vm = this;
			vm.action = 'view';

			vm.init = function() {
				var def = $q.defer();
				$http.get('/sms-template/ajax/' + $scope.org_id + '/index').then(function (response) {
					vm.template = response.data;
					def.resolve();

				});
				return def.promise;
			};

			vm.edit = function() {
				vm.original_template = angular.copy(vm.template);
				vm.action = 'edit';
			};

			vm.cancel = function() {
				vm.template = vm.original_template;
				vm.action = 'view';
				vm.errors = [];
			};

			vm.save = function() {
				$http.post('/sms-template/ajax/' + $scope.org_id + '/save/', $.param({
					data: JSON.stringify(vm.template)
				})).then(function (result) {
					if (result.data.id) {
						vm.init();
						vm.action = 'view';
					} else if (result.data.errors) {
						vm.errors = result.data.errors.split(';');
					}
				});
			};
		}]);

})(opakeApp, angular);
