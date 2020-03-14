// Create case
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ModalCaseCrtl', [
		'$scope',
		'$controller',
		'$http',
		'$location',
		'$uibModalInstance',
		'View',
		'Cases',
		'Calendar',
		'Patient',
		'uiCalendarConfig',
		'CaseRegistrationConst',
		'item',
		'CaseCancellation',
		'BeforeUnload',
		'Permissions',
		'CaseCalendarService',
		function ($scope, $controller, $http, $location, $uibModalInstance, View, Cases, Calendar, Patient, uiCalendarConfig, CaseRegistrationConst, item, CaseCancellation, BeforeUnload, Permissions, CaseCalendarService) {

			$controller('ModalCrtl', {$scope: $scope, $uibModalInstance: $uibModalInstance});

			$scope.caseRegistrationConst = CaseRegistrationConst;

			var vm = this;
			vm.item = item;
			vm.originalItem = angular.copy(vm.item);
			vm.saveButtonDisabled = false;
			vm.showAdditionalProcedures = Boolean(item.additional_cpts);
			vm.calendarService = Calendar;
			vm.hasCaseDeleteAccess =  Permissions.hasAccess('cases', 'delete');
			vm.hasCaseEditAccess =  Permissions.hasAccess('cases', 'edit');
			vm.hasCancelAppointmentAccess = Permissions.hasAccess('case_management', 'view_appointment_buttons');


			$scope.$on('modal.closing', function(e) {
				if (!angular.equals(vm.item, vm.originalItem)) {
					if(!BeforeUnload.confirm('Are you sure you want to continue without saving your changes?')) {
						e.preventDefault();
					} else {
						BeforeUnload.reset();
					}
				}
			});

			if (Calendar.patientId) {
				$http.get('/patients/ajax/' + $scope.org_id + '/patient/' + Calendar.patientId).then(function (result) {
					vm.patient = new Patient(result.data);
				});
			}

			vm.isCreation = function () {
				return !item.id;
			};

			vm.isFromBooking = function () {
				return Calendar.bookingId;
			};

			vm.changePatient = function () {
				vm.item.patient = new Patient(vm.patient);
			};

			vm.save = function () {
				if (!vm.saveButtonDisabled) {
					vm.saveButtonDisabled = true;

					checkPassed(function () {
						checkPatientAndSaveCase();
					});
				}
			};

			function checkPatientAndSaveCase() {
				var data = [];
				data.patient_id = vm.item.patient.id;
				data.time_start = moment(vm.item.time_start).format('YYYY-MM-DD');

				$http.get('/cases/ajax/' + $scope.org_id + '/hasTodayCaseForPatient/', {params: data}).then(function (result) {
					if (result.data.is_cases_exists && vm.isCreation()) {
						$scope.dialog(View.get('cases/check_patient_confirm.html'), $scope, {windowClass: 'alert'}).result.then(function () {
							saveCase();
						}, function() {
							if (Calendar.bookingId) {
								$http.post('/booking/ajax/' + $scope.org_id + '/remove/' + Calendar.bookingId);
							}
							BeforeUnload.reset(true);
							$uibModalInstance.close();
							Calendar.refetchEvents();
							$location.search('');
						});
					} else {
						saveCase();
					}
				});
			}

			function saveCase() {
				Cases.save(vm.item, function (result) {
					if (result.data.id) {
						BeforeUnload.reset(true);
						$uibModalInstance.close(result.data.registration_id);
						Calendar.refetchEvents();
						$location.search('');
						if (Calendar.isReschedule) {
							$scope.$emit('CaseSavedFromCalendar', result.data.id);
						}
					} else if (result.data.errors) {
						vm.errors = result.data.errors.split(';');
						vm.saveButtonDisabled = false;
					}
				}, Calendar.isReschedule, Calendar.bookingId);
			}

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
					$uibModalInstance.close();
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
								BeforeUnload.reset(true);
								Cases.delete(vm.item.id, function() {
									Calendar.refetchEvents();
								});
							};
							modalVm.cancel = function() {
								$uibModalInstance.dismiss('cancel');
							};
						}],
					controllerAs: 'modalVm'
				}).result.then(function () {
					$uibModalInstance.close();
				});
			};

			vm.caseDateOfServiceChanged = function(newDate) {
				CaseCalendarService.get().then(function(caseCalendar) {
					caseCalendar.fullCalendar('changeView', 'agendaDay');
					caseCalendar.fullCalendar('gotoDate', newDate);
				});
			};

			function checkPassed(confirm) {
				if (vm.item.time_start <= new Date()) {
					$scope.dialog(View.get('cases/passed_confirm.html'), $scope, {windowClass: 'alert'}).result.then(function () {
						confirm();
					}, function () {
						vm.saveButtonDisabled = false;
					});
				} else {
					confirm();
				}
			}

		}]);
})(opakeApp, angular);
