// Create case
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PreviewCaseCrtl', [
		'$scope',
		'$window',
		'Permissions',
		'View',
		'Cases',
		'Calendar',
		'CaseRegistrationConst',
		'CaseCancellation',
		function ($scope, $window, Permissions, View, Cases, Calendar, CaseRegistrationConst, CaseCancellation) {

			$scope.caseRegistrationConst = CaseRegistrationConst;

			var vm = this;

			vm.hasCaseEditAccess =  Permissions.hasAccess('cases', 'edit');
			vm.hasCaseDeleteAccess =  Permissions.hasAccess('cases', 'delete');
			vm.hasCancelAppointmentAccess = Permissions.hasAccess('case_management', 'view_appointment_buttons');

			vm.init = function (item) {
				vm.item = item;
			};

			vm.edit = function () {
				Calendar.editCase(vm.item);
			};

			vm.cancel = function () {
				var caseCancellation = new CaseCancellation({
					case_id: vm.item.id,
					patient_full_name: vm.item.patient.last_name + ', ' + vm.item.patient.first_name,
					patient_full_mrn: vm.item.patient.full_mrn,
					case_time_start: vm.item.time_start,
					case_time_end: vm.item.time_end,
					case_surgeon: vm.item.users[0].fullname
				});

				$scope.dialog(View.get('cases/confirm_cancel_appointment.html'), $scope, {
					controller: 'CaseCancellationCtrl',
					controllerAs: 'cancellationVm',
					resolve: {
						caseCancellation: caseCancellation,
						updateCancellation: false
					},
					windowClass: 'alert'
				}).result.then(function () {
					Calendar.refetchEvents();
				});
			};

			vm.delete = function () {
				$scope.dialog(View.get('cases/confirm_delete.html'), $scope, {
					windowClass: 'alert',
					controller: [
						'$scope', '$uibModalInstance',
						function($scope, $uibModalInstance) {
						var modalVm = this;
						modalVm.case = vm.item;
						modalVm.confirm = function() {
							Cases.delete(vm.item.id, function() {
								Calendar.refetchEvents();
							});
							$uibModalInstance.close();
						};
						modalVm.cancel = function() {
							$uibModalInstance.dismiss('cancel');
						};
					}],
					controllerAs: 'modalVm'
				});
			};

			vm.goToCaseManagement = function () {
				$window.location = '/cases/' + $scope.org_id + '/cm/' + vm.item.id;
			};

			vm.getFormattedAlertMessages = function () {
				var msg= '';
				angular.forEach(vm.item.alerts, function (item) {
					msg += item.message + '<br/>';
				});
				return msg;
			};


		}]);


})(opakeApp, angular);
