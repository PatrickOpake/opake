// Patient portal invitation
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PatientInvitationCrtl', [
		'$scope',
		'PatientsPortal',

		function ($scope, PatientsPortal) {
			var vm = this;
			$scope.ctrl = vm;

			vm.patient = null;

			vm.init = function (patient) {
				vm.patient = patient;
			};

			vm.isShowPortalButton = function() {
				return (vm.patient.is_patient_portal_enabled);
			};

			vm.isEnablePortalButton = function() {
				return (vm.patient.is_patient_portal_enabled && vm.patient.can_register_on_portal);
			};

			vm.getPatientPortalTitle = function() {
				return (!vm.patient.is_registered_on_portal ? "Send patient portal invite. \n Name and email required." : 'Send password reset');
			};

			vm.openPatientPortalEmailWindow = function() {
				if (vm.isEnablePortalButton()) {
					PatientsPortal.openEmailWindow($scope, vm.patient.id, vm.patient);
				}
			};

			$scope.$on('Patient.PatientSaved', function(e, patientId, patient) {
				vm.init(patient);
			});

		}]);

})(opakeApp, angular);
