(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseManagementIntakeMedicationsCtrl', [
		'$scope',
		'$http',
		'Tools',
		'BeforeUnload',
		'CaseRegistrationReconciliation',
		'CaseRegistrationConst',
		function ($scope, $http, Tools, BeforeUnload, CaseRegistrationReconciliation, CaseRegistrationConst) {

			$scope.caseRegistrationConst = CaseRegistrationConst;

			var vm = this;
			var registrationId;

			vm.isFormContentLoaded = false;

			vm.init = function (regId) {
				registrationId = regId;
				$http.get('/cases/ajax/intake/medications/' + $scope.org_id + '/reconciliation/?registration_id=' + registrationId).then(function (result) {
					vm.isFormContentLoaded = true;
					if (result.data.success) {
						vm.reconciliation = new CaseRegistrationReconciliation(result.data.reconciliation);
					} else {
						vm.reconciliation =  new CaseRegistrationReconciliation({});
						vm.reconciliation.case_anesthesia_type = result.data.anesthesia_type;
					}
					vm.originalReconciliation = angular.copy(vm.reconciliation);

					BeforeUnload.addForms(vm.originalReconciliation, vm.reconciliation, 'medications');
					BeforeUnload.add(function () {
						if (!angular.equals(vm.originalReconciliation, vm.reconciliation)) {
							return 'Are you sure you want to continue without saving your changes?';
						}
					});

				});
			};

			vm.save = function() {
				vm.isFormContentLoaded = true;
				vm.errors = null;
				$http.post('/cases/ajax/intake/medications/' + $scope.org_id + '/save?registration_id=' + registrationId, 
					$.param({data: angular.toJson(vm.reconciliation), pre_op_form: angular.toJson(blankPreOpFormObject)})
				).then(function (result) {
					if (result.data.success) {
						BeforeUnload.clearForms('medications');
						BeforeUnload.reset();
						vm.init(registrationId);
					} else if (result.data.errors) {
						vm.errors = result.data.errors;
					}
					vm.isFormContentLoaded = false;
				});
			};

			vm.addAllergy = function() {
				vm.reconciliation.addAllergy();
			};

			vm.addMedication = function() {
				vm.reconciliation.addMedication();
			};

			vm.isChanged = function () {
				return !angular.equals(vm.reconciliation, vm.originalReconciliation);
			};

			vm.cancel = function () {
				vm.reconciliation = angular.copy(vm.originalReconciliation);
			};

			vm.print = function () {
				$http.post('/cases/ajax/intake/medications/' + $scope.org_id + '/compile?registration_id=' + registrationId, $.param({data: angular.toJson(vm.reconciliation)})).then(function (result) {
					if (result.data.success) {
						Tools.print(location.protocol + '//' + location.host + result.data.url);
					}
				});
			};

			var blankPreOpFormObject = {
				'medications': [
					{
						name: ''
					},
					{
						name: ''
					},
					{
						name: ''
					}
				],
				'allergies': [
					{
						name: ''
					},
					{
						name: ''
					},
					{
						name: ''
					}
				],
				'surgeries_hospitalizations': [
					{
						name: ''
					}
				],
				'family_problems': [
					{
						name: ''
					}
				],
				'family_anesthesia_problems': [
					{
						name: ''
					}
				],
				'travel_outside': [
					{
						name: ''
					}
				]
			};

		}]);

})(opakeApp, angular);
