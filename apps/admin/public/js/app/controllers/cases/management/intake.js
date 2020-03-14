// Case view
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseIntakeCrtl', [
		'$scope',
		'$http',
		'$window',
		'View',
		'Case',
		'CaseRegistrationConst',
		'Permissions',
		'CaseCancellation',

		function ($scope, $http, $window, View, Case, CaseRegistrationConst, Permissions, CaseCancellation) {

			$scope.caseRegistrationConst = CaseRegistrationConst;

			var vm = this;

			vm.showCheckInInfo = false;
			vm.appointment_status = 0;
			vm.appointment_statuses = {
				1: 'Cancel',
				2: 'Check In',
				3: 'Reschedule'
			};

			vm.init = function (case_item) {
				vm.case = case_item;

				vm.driverLicenseUploadUrl = '/cases/ajax/intake/' + $scope.org_id + '/uploadDriversLicense/' + vm.case.id;
				vm.insuranceCardUploadUrl = '/cases/ajax/intake/' + $scope.org_id + '/uploadInsuranceCard/' + vm.case.id;

			};

			vm.changeAppointmentStatusFromDashboard = function() {
				if (vm.appointment_status == 1) {
					vm.cancelAppointment();
				} else if (vm.appointment_status == 2) {
					vm.showCheckInInfo = true;
				} else if (vm.appointment_status == 3) {
					vm.reschedule();
				}
			};

			vm.cancelAppointment = function () {
				var caseCancellation = new CaseCancellation({
					case_id: vm.case.id,
					patient_full_name: vm.case.patient.full_name,
					patient_full_mrn: vm.case.patient.full_mrn,
					case_time_start: vm.case.time_start,
					case_time_end: vm.case.time_end,
					case_surgeon: vm.case.first_surgeon_for_dashboard
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
					$scope.$emit('caseCanceled');
				}, function () {
					vm.appointment_status = 0;
				});
			};

			vm.reschedule = function() {
				$window.location = '/cases/' + $scope.org_id + '#?case=' + vm.case.id;
			};

			vm.completeCheckIn = function() {
				return $http.post('/cases/ajax/' + $scope.org_id + '/changeAppointmentStatus/' +  vm.case.id, $.param({
					newStatus: CaseRegistrationConst.APPOINTMENT_STATUS.COMPLETED,
					data: JSON.stringify(vm.case)
				})).then(function () {
					vm.case.appointment_status = CaseRegistrationConst.APPOINTMENT_STATUS.COMPLETED;
					vm.case.time_check_in = moment(new Date()).toDate();
					$scope.$emit('hideDashboardSpinner');
					
				});
			};

			vm.completeCheckInFromDashboard = function() {
				$scope.$emit('showDashboardSpinner');
				vm.completeCheckIn();
				vm.showCheckInInfo = false;
			};

			vm.reOpenCheckInFromDashboard = function() {
				$scope.dialog(View.get('cases/confirm_reopen_appointment.html'), $scope, {windowClass: 'alert'}).result.then(function () {
					return $http.post('/cases/ajax/' + $scope.org_id + '/changeAppointmentStatus/' +  vm.case.id, $.param({
						newStatus: CaseRegistrationConst.APPOINTMENT_STATUS.NEW,
						data: JSON.stringify(vm.case)
					})).then(function () {
						vm.case.appointment_status = CaseRegistrationConst.APPOINTMENT_STATUS.NEW;
						vm.appointment_status = 0;
						vm.showCheckInInfo = false;
					});
				});
			};

			vm.cancelCheckIn = function() {
				vm.appointment_status = 0;
				vm.showCheckInInfo = false;
			};

			vm.preview = function(doc) {
				vm.doc = doc;
				$scope.dialog(View.get('/cases/cm/intake/doc_preview.html'), $scope, {size: 'lg', windowClass: 'preview-doc'});
			};

			vm.removeDriversLicense = function () {
				$http.get('/cases/ajax/intake/' + $scope.org_id + '/removeDriversLicense/' + vm.case.id).then(function () {
					vm.refreshFiles();
				});
			};

			vm.removeInsuranceCard = function () {
				$http.get('/cases/ajax/intake/' + $scope.org_id + '/removeInsuranceCard/' + vm.case.id).then(function () {
					vm.refreshFiles();
				});
			};

			vm.getPrintDocUrl = function() {
				return vm.doc.url + '&to_download=false';
			};

			vm.refreshFiles = function() {
				var accompanied_by = vm.case.accompanied_by;
				var accompanied_phone = vm.case.accompanied_phone;
				var accompanied_email = vm.case.accompanied_email;
				$http.get('/cases/ajax/' + $scope.org_id + '/case/' + vm.case.id).then(function (result) {
					var data = result.data;
					vm.case = new Case(data);
					vm.case.accompanied_by = accompanied_by;
					vm.case.accompanied_phone = accompanied_phone;
					vm.case.accompanied_email = accompanied_email;
				});
			};

			vm.openCheckInInfo = function () {
				if (Permissions.hasAccess('case_management', 'view_appointment_buttons')) {
					vm.showCheckInInfo = !vm.showCheckInInfo;
				}
			};

		}]);

})(opakeApp, angular);
