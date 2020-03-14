(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('InsurancePayorsEditCtrl', [
		'$scope',
		'$uibModalInstance',
		'$http',
		'$q',
		'View',
		'InsurancePayor',
		'InsurancePayorAddress',
		'PatientConst',
		'payorId',
		function ($scope, $uibModalInstance, $http, $q, View, InsurancePayor, InsurancePayorAddress, PatientConst, payorId) {
			$scope.patientConst = PatientConst;

			var vm = this;

			vm.payorId = payorId;
			vm.payor = null;
			vm.errors = null;
			vm.patientConst = PatientConst;

			vm.address = null;
			vm.origAddress = null;
			vm.addressAction = 'view';

			vm.init = function (payorId) {
				vm.payorId = payorId;
				if (payorId) {
					$http.get('/settings/databases/insurance-payors/ajax/getById/' + vm.payorId).then(function (response) {
						vm.payor = new InsurancePayor(response.data.data);
					});
				}
				else {
					vm.payor = new InsurancePayor();
				}
			};

			vm.init(vm.payorId);

			vm.cancel = function() {
				$uibModalInstance.dismiss('cancel');
			};

			vm.save = function () {
				vm.errors = [];
				$http.post('/settings/databases/insurance-payors/ajax/save/', $.param({
					data: JSON.stringify(vm.payor)
				})).then(function (result) {
					if (result.data.success) {
						$uibModalInstance.close();
					}
					else {
						vm.errors = result.data.errors;
					}
				}, function (error) {
					vm.errors = ['Internal Error: ' + error.data.message];
				});
			};

			vm.createNewAddress = function () {
				vm.addressAction = 'create';
				vm.address = new InsurancePayorAddress();
			};

			vm.editAddress = function(address, event) {
				vm.addressAction = 'edit';
				vm.origAddress = address;
				vm.address = angular.copy(address);
			};

			vm.deleteAddress = function(address, event) {
				$scope.dialog(View.get('settings/databases/insurance-payors/confirm-delete-address.html'),
					$scope, {windowClass: 'alert'})
					.result.then(function() {
						vm.payor.deleteAddress(address);
					});
			};

			vm.saveCurrentAddress = function() {
				if (vm.addressAction == 'create') {
					vm.payor.addAddress(new InsurancePayorAddress(vm.address));
				}
				else {
					vm.payor.updateAddress(vm.origAddress, vm.address);
				}

				vm.closeAddressEdit();
			};

			vm.collapseCurrentAddress = function() {
				vm.closeAddressEdit();
			};

			vm.closeAddressEdit = function () {
				vm.addressAction = 'view';
				vm.address = null;
				vm.origAddress = null;
			};
		}]);

})(opakeApp, angular);
