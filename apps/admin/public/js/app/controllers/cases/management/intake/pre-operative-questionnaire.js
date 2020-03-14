(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CasePatientFormsPreOperativeCtrl', [
		'$rootScope',
		'$controller',
		'$http',
		'BeforeUnload',
		'PreOperativeForm',
		'CaseRegistrationReconciliation',
		function ($rootScope, $controller, $http, BeforeUnload, PreOperativeForm, CaseRegistrationReconciliation) {

			var vm = this;
			vm.registrationId = null;
			$controller('CasesFormsPreOperativeCtrl', {vm: vm});


			vm.init = function(registrationId) {
				vm.registrationId = registrationId;
				$http.get('/cases/ajax/intake/pre-operative/' + $rootScope.org_id + '/getForm?registration_id=' + registrationId).then(function(res) {
					if (res.data.success) {
						vm.form = new PreOperativeForm(res.data.form);
					} else {
						vm.form = angular.copy(vm.newForm)
					}

					vm.originalForm = angular.copy(vm.form);
					BeforeUnload.addForms(vm.originalForm, vm.form, 'interview');
				});
			};

			vm.save = function() {
				vm.errors = null;
				var blankReconciliationObject = new CaseRegistrationReconciliation({});
				var params = $.param({
					data: angular.toJson(vm.form),
					reconciliation: angular.toJson(blankReconciliationObject)
				});
				$http.post('/cases/ajax/intake/pre-operative/' + $rootScope.org_id + '/saveForm?registration_id=' + vm.registrationId, params).then(function (res) {
					if (res.data.success) {
						BeforeUnload.clearForms('interview');
						$rootScope.subTopMenuActive = 'case_details';
					} else if (res.data.errors) {
						vm.errors = res.data.errors;
					}
				});
			};

			vm.print = function() {

			};

			vm.cancel = function() {
				BeforeUnload.clearForms('interview');
				$rootScope.subTopMenuActive = 'case_details';
			};

			vm.getBmi = function() {
				if (vm.form) {
					if ((vm.form.height_ft || vm.form.height_in) && vm.form.weight_lbs) {
						var weightLbs = parseInt(vm.form.weight_lbs, 10);
						var heightFt = vm.form.height_ft ? parseInt(vm.form.height_ft, 10) : 0;
						var heightIn = vm.form.height_in ? parseInt(vm.form.height_in, 10) : 0;
						var heightInTotal = ((heightFt * 12) + heightIn);

						if (weightLbs > 0 && heightInTotal > 0) {
							var value = (weightLbs / Math.pow(heightInTotal, 2)) * 703;
							if (value > 100) {
								value = 100;
							}

							return Math.round(value * 100) / 100;
						}
					}
				}

				return '';
			}

		}]);

})(opakeApp, angular);
