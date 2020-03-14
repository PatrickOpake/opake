// Dashboard list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('DashboardListCrtl', [
		'$rootScope',
		'$scope',
		'$http',
		'$controller',
		'$filter',
		'$window',
		'$httpParamSerializer',
		'$timeout',
		'View',
		'Case',
		'Permissions',
		'ViewState',
		'CaseNotes',
		'InServiceNotes',
		'CaseInService',
		'Cases',
		'PatientConst',

		function ($rootScope, $scope, $http, $controller, $filter, $window, $httpParamSerializer, $timeout, View, Case, Permissions, ViewState, CaseNotes, InServiceNotes, CaseInService, Cases, PatientConst) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			var responseData = null,
				surgeons,
				rooms,
				highlightPeriodStart,
				highlightPeriodEnd;

			vm.date = ViewState.getCasesViewDate();

			vm.hasCaseManagementAccess = Permissions.hasAccess('case_management', 'view');
			vm.hasCaseChangeStatusAccess = Permissions.hasAccess('case_management', 'view_appointment_buttons');

			vm.isFilterShowed = false;
			vm.isSettingsShowed = false;
			vm.notesForCases = [];

			vm.isShowLoading = false;
			vm.isShowSninner = false;
			vm.isShowHeavyElements = false;
			vm.showIconsOnPhone = false;

			// Inicialization
			var viewType = ViewState.getState('cases_view_type') || 'day';
			if (viewType === 'month') {
				viewType = 'day';
				ViewState.update('cases_view_type', viewType);
			}
			vm.viewType = viewType;
			vm.groupType = ViewState.getState('dashboard_group') || 'surgeon';

			$http.get('/overview/ajax/dashboard/' + $scope.org_id + '/initData/').then(function (resp) {
				surgeons = resp.data.surgeons;
				rooms = resp.data.rooms;
				vm.displayTimestamp = resp.data.display_timestamp;
				vm.displayPointOfContact = resp.data.display_point_of_contact;
				vm.pointOfContactTemplateMsg = resp.data.point_of_contact_msg;

				search();
			});

			// Events
			$scope.$on('showDashboardSpinner', function () {
				vm.isShowSninner = true;
			});

			$scope.$on('hideDashboardSpinner', function () {
				vm.isShowSninner = false;
			});

			$scope.$on('caseCanceled', function () {
				search();
			});

			$rootScope.$on('caseList.calendarDateChanged', function(e, date) {
				vm.viewType = 'day';
				vm.updateDate(date);
				search({
					isSideCalendar: true
				});
			});

			// VM actions
			vm.search = search;

			vm.updateDate = function (newDate) {
				vm.date = newDate;
				ViewState.updateCasesViewDate(newDate);
			};

			vm.updateNotesForCases = function() {
				CaseNotes.getNotesForCases(vm.case_ids).then(function (result) {
					vm.notesForCases = result;
				});
			};

			vm.getPrintUrl = function () {
				var params = angular.copy(vm.search_params);
				params.dos = moment(vm.date).format('YYYY-MM-DD');
				params.start_of_week = getStartOfWeek();
				params.end_of_week = getEndOfWeek();
				params.view_type = vm.viewType;
				return '/cases/ajax/' + $scope.org_id + '/exportOverview/?' + $httpParamSerializer(params) + '&to_download=false';
			};

			vm.showFilter = function () {
				vm.isFilterShowed = !vm.isFilterShowed;
				vm.isSettingsShowed = false;
			};

			vm.showSettings = function () {
				if (!vm.settingsSurgeons) {
					vm.settingsSurgeons = angular.copy(surgeons);
					vm.surgeonDisplayPositions = $filter('range')([], 1, surgeons.length).map(String);
				}
				if (!vm.settingsRooms) {
					vm.settingsRooms = angular.copy(rooms);
					vm.roomDisplayPositions = $filter('range')([], 1, rooms.length).map(String);
				}

				vm.isSettingsShowed = !vm.isSettingsShowed;
				vm.isFilterShowed = false;
				vm.displaySettingsType = vm.groupType;
			};

			vm.saveSettings = function() {
				var displaySettings = {surgeons: vm.settingsSurgeons, rooms: vm.settingsRooms, display_timestamp: vm.displayTimestamp};
				$http.post('/overview/ajax/dashboard/' + $scope.org_id + '/updateDisplaySettings/', $.param({
					data: JSON.stringify(displaySettings)
				})).then(function () {
					surgeons = angular.copy(vm.settingsSurgeons);
					rooms = angular.copy(vm.settingsRooms);
					vm.isSettingsShowed = false;
					search();
				});
			};

			vm.isToday = function () {
				return ((new Date()).toDateString() === vm.date.toDateString());
			};

			vm.today = function () {
				vm.updateDate(new Date());
				search();
			};

			vm.previous = function () {
				if (vm.viewType == 'day') {
					vm.updateDate(moment(vm.date).add(-1, 'days').toDate());
				} else if (vm.viewType == 'week') {
					vm.updateDate(moment(vm.date).add(-1, 'weeks').toDate());
				}
				search();
			};

			vm.next = function () {
				if (vm.viewType == 'day') {
					vm.updateDate(moment(vm.date).add(1, 'days').toDate());
				} else if (vm.viewType == 'week') {
					vm.updateDate(moment(vm.date).add(1, 'weeks').toDate());
				}
				search();
			};

			vm.isViewTypeDay = function () {
				return vm.viewType == 'day';
			};

			vm.isViewTypeWeek = function () {
				return vm.viewType == 'week';
			};

			vm.setViewTypeDay = function () {
				if (!vm.isViewTypeDay()) {
					vm.viewType = 'day';
					search();
				}
			};

			vm.setViewTypeWeek = function () {
				if (!vm.isViewTypeWeek()) {
					vm.viewType = 'week';
					search();
				}
			};

			vm.getDateDisplay = function () {
				if (View.isTablet() || View.isPhone()) {
					if (vm.isViewTypeDay()) {
						return moment(vm.date).format('ddd - MMM. D, YYYY');
					} else if (vm.isViewTypeWeek()) {
						if (moment(getStartOfWeek()).format('M') == moment(getEndOfWeek()).format('M')) {
							return (moment(getStartOfWeek()).format('MMM. D') + ' - ' + moment(getEndOfWeek()).format('D'));
						} else {
							return (moment(getStartOfWeek()).format('MMM. D') + ' - ' + moment(getEndOfWeek()).format('MMM. D'));
						}
					}
				} else {
					if (vm.isViewTypeDay()) {
						return moment(vm.date).format('dddd MMMM D, YYYY');
					} else if (vm.isViewTypeWeek()) {
						if (moment(getStartOfWeek()).format('M') == moment(getEndOfWeek()).format('M')) {
							return (moment(getStartOfWeek()).format('MMMM D') + ' - ' + moment(getEndOfWeek()).format('D'));
						} else {
							return (moment(getStartOfWeek()).format('MMMM D') + ' - ' + moment(getEndOfWeek()).format('MMMM D'));
						}
					}
				}
			};

			vm.getStartTimeFormat = function () {
				return vm.isViewTypeDay() ? 'h:mm a' : 'M/d/yyyy h:mm a';
			};

			vm.isStartTimeHighlightInRed = function (case_item) {
				var time = case_item.time_start;
				if (highlightPeriodEnd >= time && highlightPeriodStart < time && case_item.appointment_status == 0) {
					return true;
				}
				return false;
			};

			vm.getPatientTooltipStr = function (patient) {
				var tooltipStr = '';
				if (patient.dob) {
					tooltipStr += 'DOB: ' + moment((patient.dob)).format('MM/DD/YYYY') + '<br/>';
				}
				if (patient.home_phone) {
					tooltipStr += 'Phone: ' + splitPhone(patient.home_phone) + '<br/>';
				} if (patient.full_mrn) {
					tooltipStr += 'MRN: ' + patient.full_mrn + ' ' + patient.sex_letter;
				}

				return tooltipStr;
			};

			vm.changeGroupType = function () {
				ViewState.update('dashboard_group', vm.groupType);
				updateDashboardView();
			};

			vm.goToCaseManagement = function (id) {
				$window.location = '/cases/' + $scope.org_id + '/cm/' + id;
			};

			vm.scrollToTop = function() {
				$("html, body").animate({scrollTop: 0}, "slow");
			};

			vm.sendSMS = function (caseItem) {
				$scope.dialog(View.get('cases/confirm_sending_sms.html'), $scope, {
					windowClass: 'alert',
					controller: [
						'$scope', '$uibModalInstance', 'Cases',
						function($scope, $uibModalInstance, Cases) {
							var modalVm = this;
							modalVm.case = caseItem;
							modalVm.pointOfContactMsg = Cases.replaceDynamicFieldsSMSTemplate(caseItem, vm.pointOfContactTemplateMsg);
							modalVm.save = function() {
								$scope.$emit('showDashboardSpinner');
								$http.get('/cases/ajax/' + $scope.org_id + '/pointContactSMS/' + modalVm.case.id).then(function (resp) {
									$uibModalInstance.dismiss('ok');
									$scope.$emit('hideDashboardSpinner');
									if(resp.data.success) {
										Cases.showSendedSMSMessage(modalVm.case);
										modalVm.case.is_sent_sms = true;
									}
								});
							};

							modalVm.cancel = function() {
								$uibModalInstance.dismiss('cancel');
							};
						}],
					controllerAs: 'modalVm'
				});
			};

			vm.validatePointOfContactPhone = function (caseItem) {
				return caseItem.point_of_contact_phone && caseItem.point_of_contact_phone.length === 10  && caseItem.point_of_contact_phone_type == PatientConst.TYPE_PHONE_NAMES.CELL;
			};

			// Internal functions
			function search(options) {
				options = options || {};

				vm.group_cases = null;
				vm.isShowLoading = true;
				vm.isShowHeavyElements = false;

				var data = angular.copy(vm.search_params);
				data.dos = moment(vm.date).format('YYYY-MM-DD');
				data.start_of_week = getStartOfWeek();
				data.end_of_week = getEndOfWeek();
				data.view_type = vm.viewType;

				var eventOptions = {};
				if (options.isSideCalendar) {
					eventOptions.isSideCalendarChanged = true;
				}
				emitLoadCasesEvent(eventOptions);

				$http.get('/overview/ajax/dashboard/' + $scope.org_id + '/index/', {params: data}).then(function (response) {
					responseData = response.data;
					updateDashboardView();

					vm.isShowLoading = false;
					vm.case_ids = response.data.case_ids;
					CaseNotes.getUnreadNotes(response.data.case_ids);

					$timeout(function(){
						vm.isShowHeavyElements = true;
					});
				});
			}

			function emitLoadCasesEvent(options) {

				options = options || {};
				options.viewType = vm.viewType;

				if (vm.viewType == 'week') {
					options.date = moment(vm.date).startOf('week').toDate();
					options.viewType = 'week';
				} else {
					options.date = vm.date;
					options.viewType = 'day';
				}

				$scope.$emit('caseList.loadCases', options);
			}

			function getStartOfWeek () {
				return moment(vm.date).startOf('week').format('YYYY-MM-DD');
			};

			function getEndOfWeek () {
				return moment(vm.date).endOf('week').format('YYYY-MM-DD');
			};

			function updateDashboardView () {
				if (responseData) {
					var group_cases = [];

					var inServices = [];
					var inServicesIds = [];
					angular.forEach(responseData.in_services, function (in_service) {
						var inServiceObject = new CaseInService(in_service);
						inServiceObject.showMoreActions = false;
						inServices.push(new CaseInService(inServiceObject));
						inServicesIds.push(inServiceObject.id);
					});
					InServiceNotes.getUnreadNotes(inServicesIds);

					if (vm.groupType == 'surgeon') {
						var surgeonsForCases = surgeons;
						if (vm.search_params.doctor) {
							surgeonsForCases = $filter('filter')(surgeons, {id: vm.search_params.doctor});
						}
						angular.forEach(surgeonsForCases, function (surgeon) {
							var cases = [];

							angular.forEach(responseData.cases, function (case_item) {
								var relations = responseData.relations;
								var relationsKeepGoing = true;
								angular.forEach(relations, function (relation) {
									if (((relation.user_id == surgeon.id) || (relation.staff_id == surgeon.id) || (relation.assistant_id == surgeon.id))
										&& (relation.case_id == case_item.id) && relationsKeepGoing) {
										var caseObject = new Case(case_item);
										caseObject.showMoreActions = false;
										cases.push(caseObject);
										relationsKeepGoing = false;
									}
								});
							});

							if (cases.length) {
								group_cases.push({
									header: surgeon.full_name + getCasesLengthStr(cases.length),
									cases: cases,
									open: true,
									position: surgeon.overview_display_position
								});
							}
						});

						if (inServices.length) {
							var inServicesObject = {header: 'In Service', cases: inServices, open: true};
							group_cases.push(inServicesObject);
						}
					}

					if (vm.groupType == 'room') {
						angular.forEach(rooms, function (room) {
							var cases = [];

							angular.forEach(responseData.cases, function (case_item) {
								if (room.id == case_item.location.id) {
									var caseObject = new Case(case_item);
									caseObject.showMoreActions = false;
									cases.push(caseObject);
								}
							});

							angular.forEach(inServices, function (in_service) {
								if (room.id == in_service.location_id) {
									cases.push(in_service);
								}
							});

							if (cases.length) {
								cases = $filter('orderBy')(cases, 'time_start');
								group_cases.push({
									header: room.name + getCasesLengthStr(cases.length),
									cases: cases,
									open: true,
									position: room.overview_display_position
								});
							}
						});
					}

					vm.group_cases = group_cases.sort(groupCasesArraySortByPosition);
					updateHighlightPeriod();
				}
			};

			function groupCasesArraySortByPosition (a, b) {
				if (!a.position && !b.position) {
					return groupCasesArraySortByCasesCount(a, b);
				} else if (!a.position) {
					return 1;
				} else if (!b.position) {
					return -1;
				} else if (a.position > b.position) {
					return 1;
				} else if (a.position < b.position) {
					return -1;
				}

				return groupCasesArraySortByCasesCount(a, b);
			}

			function groupCasesArraySortByCasesCount (a, b) {
				if (a.cases.length === b.cases.length) {
					return a.header > b.header ? 1 : -1;
				}
				return a.cases.length > b.cases.length ? 1 : -1;
			}

			function getCasesLengthStr (casesLength) {
				if (casesLength == 1) {
					return ' (1 case)';
				}
				return ' (' + casesLength + ' cases)';
			}

			function splitPhone(phone) {
				var result = phone.slice(0, 3) + '-' + phone.slice(3, 6) + '-' + phone.slice(6, 10);
				return result.trim();
			}

			function updateHighlightPeriod() {
				var now = new Date();
				highlightPeriodStart = moment(now).add(-45, 'minutes');
				highlightPeriodEnd = moment(now).add(15, 'minutes');
			}
		}]);

})(opakeApp, angular);
