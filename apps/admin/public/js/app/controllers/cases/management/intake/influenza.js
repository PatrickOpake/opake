(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CasePatientFormsInfluenzaCtrl', [
		'$rootScope',
		'$controller',
		'$http',
		'Tools',
		'BeforeUnload',
		function ($rootScope, $controller, $http, Tools, BeforeUnload) {

			var vm = this;
			$controller('CasesFormsInfluenzaCtrl', {vm: vm});
			var registrationId;

			vm.initData = function (regId) {
				registrationId = regId;
				vm.loadFormSrc = '/cases/ajax/intake/influenza/' + $rootScope.org_id + '/getForm?registration_id=' + registrationId;
				vm.init().then(function () {
					vm.originalForm = angular.copy(vm.form);
					BeforeUnload.addForms(vm.originalForm , vm.form, 'influenza');
				});
			};

			vm.save = function() {
				vm.errors = null;
				$http.post('/cases/ajax/intake/influenza/' + $rootScope.org_id + '/saveForm?registration_id=' + registrationId, $.param({data: angular.toJson(vm.form)})).then(function (res) {
					if (res.data.success) {
						BeforeUnload.clearForms('influenza');
						$rootScope.subTopMenuActive = 'case_details';
					} else if (res.data.errors) {
						vm.errors = res.data.errors;
					}
				});
			};

			vm.print = function () {
				$http.post('/cases/ajax/intake/influenza/' + $rootScope.org_id + '/compileForm?registration_id=' + registrationId, $.param({data: angular.toJson(vm.form)})).then(function (res) {
					if (res.data.success) {
						Tools.print(location.protocol + '//' + location.host + res.data.url);
					}
				});
			};

			vm.cancel = function() {
				BeforeUnload.clearForms('influenza');
				$rootScope.subTopMenuActive = 'case_details';
			};

		}]);

})(opakeApp, angular);
