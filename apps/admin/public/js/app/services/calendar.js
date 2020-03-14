// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('Calendar', [
		'$window',
		'$location',
		'$rootScope',
		'$uibModal',
		'$timeout',
		'View',
		'Permissions',
		'uiCalendarConfig',
		'Cases',
		'Case',
		'CaseBlocking',
		'Booking',
		'Bookings',
		'CaseInService',
		'CaseCalendarService',
		function ($window, $location, $rootScope, $uibModal, $timeout, View, Permissions, uiCalendarConfig,
				  Cases, Case, CaseBlocking, Booking, Bookings, CaseInService, CaseCalendarService) {

			var self = this;
			self.action = 'calendar';
			self.showCalendar = false;
			self.isReschedule = false;
			self.cancellationId = null;
			self.bookingId = null;

			this.iniCase = function () {
				var params = $location.search();
				if (params.case) {
					self.isReschedule = true;
					if (params.cancellation) {
						self.cancellationId = params.cancellation;
					}
					Cases.get(params.case).then(function (item) {
						self.editCase(item);
						if (item.time_start) {
							$rootScope.$broadcast('calendarCaseRescheduled', item);
						}
					});
				} else if (params.patient && Permissions.hasAccess('cases', 'create')) {
					self.patientId = params.patient;
					self.createCase();
				} else if(params.booking_id  && Permissions.hasAccess('cases', 'create')) {
					self.bookingId = params.booking_id;
					Bookings.get(params.booking_id).then(function (booking) {
						var caseItem = Cases.getCaseFromBooking(booking);
						CaseCalendarService.get().then(function(caseCalendar) {
							caseCalendar.fullCalendar('changeView', 'agendaDay');
							caseCalendar.fullCalendar('gotoDate', caseItem.time_start || moment({
								hour: 12,
								minute: 0
							}).toDate());
						});
						self.createCase(caseItem);
					});
				}
			};

			this.setAction = function (action) {
				self.action = self.action === action ? 'calendar' : action;
			};

			this.getAction = function () {
				return self.action;
			};

			this.reset = function () {
				self.setAction('calendar');
			};

			this.refetchEvents = function () {
				CaseCalendarService.get().then(function(calendar) {
					if (calendar) {
						calendar.fullCalendar('refetchEvents');
					}
				});
			};


			this.createCase = function (newCase) {
				if (!newCase) {
					newCase = new Case();
				}
				self.newCase = newCase;
				showCaseForm(newCase);
			};

			this.editCase = function (item) {
				self.editedCase = item;
				showCaseForm(item);
			};

			this.editInService = function (item) {
				if (!item) {
					item = new CaseInService();
				}
				self.newInService = item;
				showInServiceForm(item);
			};

			this.editBlock = function (blocking) {
				showBlockingForm(blocking);
			};

			this.editBlockItem = function (blockingItem) {
				$uibModal.open({
					controller: 'ModalCaseBlockingItemCtrl',
					controllerAs: 'blockItemVm',
					templateUrl: View.get('cases/calendar/blocking/item_form.html'),
					size: 'md',
					windowClass: 'cases-calendar--blocking-modal',
					backdropClass: 'transparent',
					resolve: {
						blockingItem: blockingItem
					}
				});
			};

			this.createBlock = function () {
				var blocking = new CaseBlocking();
				showBlockingForm(blocking);
			};

			this.showModalWithButtons = function (item) {
				showModalWithButtons(item);
			};

			function showCaseForm(item) {
				var reg_id = null;
				var modal = $uibModal.open({
					controller: 'ModalCaseCrtl',
					controllerAs: 'caseVm',
					templateUrl: View.get('cases/calendar/case_form.html'),
					size: 'md',
					windowClass: 'cases-calendar--modal',
					openedClass: 'modal-open--scrolling-under-modal',
					backdropClass: 'transparent',
					animation: false,
					resolve: {
						item: item
					}
				});

				modal.result.then(function (regId) {
					reg_id = regId;
				}, function () {
					$scope.$emit('CaseChangedFromCalendarCancelled', item.id);
				}).finally(function () {
					delete self.newCase;
					delete self.editedCase;
				});
				modal.closed.then(function () {
					if (reg_id) {
						showScheduledAlert(item, reg_id);
					}
				});
			}

			function showScheduledAlert(item, regId) {
				$uibModal.open({
					controller: function ($scope, $controller, $uibModalInstance, item) {
						$controller('ModalCrtl', {$scope: $scope, $uibModalInstance: $uibModalInstance});
						var vm = this;
						vm.item = item;
					},
					controllerAs: 'alertVm',
					templateUrl: View.get('cases/calendar/scheduled_alert.html'),
					size: 'md',
					windowClass: 'cases-calendar--modal-alert',
					backdropClass: 'transparent',
					animation: false,
					resolve: {
						item: item
					}
				}).result.then(function () {
					$window.location = '/cases/registrations/' + $rootScope.org_id + '/view/' + regId;
				});
			}

			function showBlockingForm(blocking) {
				$uibModal.open({
					controller: 'ModalCaseBlockingCtrl',
					controllerAs: 'blockingVm',
					templateUrl: View.get('cases/calendar/blocking/form.html'),
					size: 'md',
					windowClass: 'cases-calendar--blocking-modal',
					openedClass: 'modal-open--scrolling-under-modal',
					backdropClass: 'transparent',
					resolve: {
						blocking: blocking,
						blockingItem: null
					}
				});
			}

			function showInServiceForm(item) {
				var modal = $uibModal.open({
					controller: 'ModalInServiceCrtl',
					controllerAs: 'serviceVm',
					templateUrl: View.get('cases/calendar/in_service_form.html'),
					size: 'md',
					windowClass: 'cases-calendar--modal',
					backdropClass: 'transparent',
					resolve: {
						item: item
					}
				});

				modal.closed.then(function () {
					delete self.newInService;
				});
			}

			function showModalWithButtons(item) {
				$uibModal.open({
					controller: function ($scope, $controller, $uibModalInstance, item) {
						$controller('ModalCrtl', {$scope: $scope, $uibModalInstance: $uibModalInstance});
						var vm = this;
						vm.item = item;

						vm.createBooking = function () {
							var url = '/booking/' + $rootScope.org_id + '/create/';
							var params = '#?';
							if(item.location && item.location.id) {
								params += 'location=' + item.location.id + '&';
							}
							if(item.start) {
								params += 'start=' + moment(item.start).format('YYYY-MM-DD HH:mm:ss') + '&';
							}
							if(item.end) {
								params += 'end=' + moment(item.end).format('YYYY-MM-DD HH:mm:ss') + '&';
							}
							$window.location = url + params;
						};
					},
					controllerAs: 'buttonVm',
					templateUrl: View.get('cases/calendar/modal-buttons.html'),
					size: 'sm',
					windowClass: 'cases-calendar--modal modal-buttons',
					backdropClass: 'transparent',
					resolve: {
						item: item
					}
				}).result.then(function () {
					showInServiceForm(item);
				});
			}

		}]);
})(opakeApp, angular);
