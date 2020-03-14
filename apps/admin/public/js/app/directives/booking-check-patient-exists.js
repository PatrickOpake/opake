(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('bookingCheckPatientExists', ['$rootScope', '$http', 'View', '$uibModal', function ($rootScope, $http, View, $uibModal) {
		return {
			restrict: "A",
			scope: {
				bookingCheckPatientExists: '='
			},
			replace: true,
			link: function (scope, element, attrs) {
				element.find('input').blur(function () {
					var patient = scope.bookingCheckPatientExists;
					if (!patient.id && patient.first_name && patient.last_name && patient.dob) {
						$http.post('/booking/ajax/' + $rootScope.org_id + '/getExistingPatients/', $.param({data: JSON.stringify(patient)})).then(function (result) {
							if (result.data.length) {
								$uibModal.open({
									controller: function ($scope, $controller, $uibModalInstance) {
										$controller('ModalCrtl', {$scope: $scope, $uibModalInstance: $uibModalInstance});
										var vm = this;
										vm.existingPatients = result.data;

										vm.selectPatient = function (patientId) {
											$rootScope.$broadcast('Booking.ExistingPatientSelected', patientId);
											$uibModalInstance.close();
										};
									},
									controllerAs: 'existingPatientsVm',
									templateUrl: View.get('booking/existing_patients.html'),
									size: 'sm',
									windowClass: 'booking-existing-patients-modal',
									backdropClass: 'transparent'
								});
							}
						});
					}
				});
			}
		};
	}]);

})(opakeCore, angular);