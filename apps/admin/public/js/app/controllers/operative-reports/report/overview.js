// Overview reports
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('OperativeReportsOverviewCtrl', [
		'$rootScope',
		'$scope',
		'$http',
		'$controller',
		'$window',
		'$filter',
		'Case',
		'ViewState',

		function ($rootScope, $scope, $http, $controller, $window, $filter, Case, ViewState) {

			var vm = this;
			$controller('ListCrtl', {vm: vm});

			vm.date = ViewState.getCasesViewDate();

			vm.showWaitingSpinner = false;
			vm.dataLoaded = false;

			$scope.$on('showDashboardSpinner', function () {
				vm.showWaitingSpinner = true;
			});

			$scope.$on('hideDashboardSpinner', function () {
				vm.showWaitingSpinner = false;
			});

			$scope.$on('caseCanceled', function () {
				vm.search();
			});

			$rootScope.$on('caseList.calendarDateChanged', function(e, date) {
				vm.viewType = 'day';
				vm.updateDate(date);
				vm.search({
					isSideCalendar: true
				});
			});

			var getStartOfWeek = function () {
				return moment(vm.date).startOf('week').format('YYYY-MM-DD');
			};

			var getEndOfWeek = function () {
				return moment(vm.date).endOf('week').format('YYYY-MM-DD');
			};

			vm.updateDate = function (newDate) {
				vm.date = newDate;
				ViewState.updateCasesViewDate(newDate);
			};

			vm.search = function (options) {

				options = options || {};

				vm.dataLoaded = false;
				if (!vm.viewType) {
					vm.viewType = ViewState.getState('cases_view_type') || 'day';
				}
				var data = angular.copy(vm.search_params);
				data.dos = moment(vm.date).format('YYYY-MM-DD');
				data.start_of_week = getStartOfWeek();
				data.end_of_week = getEndOfWeek();
				data.view_type = vm.viewType;
				data.non_surgeon = true;
				if($scope.listVm.user_id) {
					data.user_id = $scope.listVm.user_id;
				}

				var eventOptions = {};
				if (options.isSideCalendar) {
					eventOptions.isSideCalendarChanged = true;
				}
				emitLoadCasesEvent(eventOptions);

				return $http.get('/operative-reports/ajax/' + $scope.org_id + '/overview/', {params: data}).then(function (response) {
					vm.dataLoaded = true;
					var data = response.data;
					vm.items = [];

					angular.forEach(data.items, function(item) {
						var caseItem = new Case(item);
						vm.items.push(caseItem);
					});
				});
			};

			vm.isToday = function () {
				return ((new Date()).toDateString() === vm.date.toDateString());
			};

			vm.today = function () {
				vm.updateDate(new Date());
				vm.search();
			};

			vm.previous = function () {
				if (vm.viewType == 'day') {
					vm.updateDate(moment(vm.date).add(-1, 'days').toDate());
				} else if (vm.viewType == 'week') {
					vm.updateDate(moment(vm.date).add(-1, 'weeks').toDate());
				}
				vm.search();
			};

			vm.next = function () {
				if (vm.viewType == 'day') {
					vm.updateDate(moment(vm.date).add(1, 'days').toDate());
				} else if (vm.viewType == 'week') {
					vm.updateDate(moment(vm.date).add(1, 'weeks').toDate());
				}
				vm.search();
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
					vm.search();
				}
			};

			vm.setViewTypeWeek = function () {
				if (!vm.isViewTypeWeek()) {
					vm.viewType = 'week';
					vm.search();
				}
			};

			vm.getDateDisplay = function () {
				if (vm.isViewTypeDay()) {
					return moment(vm.date).format('dddd MMMM D, YYYY');
				} else if (vm.isViewTypeWeek()) {
					if (moment(getStartOfWeek()).format('M') == moment(getEndOfWeek()).format('M')) {
						return (moment(getStartOfWeek()).format('MMMM D') + ' - ' + moment(getEndOfWeek()).format('D'));
					} else {
						return (moment(getStartOfWeek()).format('MMMM D') + ' - ' + moment(getEndOfWeek()).format('MMMM D'));
					}
				}
			};

			vm.isStartTimeHighlightInRed = function (case_item) {
				var current = new Date();
				var time = case_item.time_start;
				if ((moment(current).add(15, 'minutes') >= time)
					&& (moment(current).add(-45, 'minutes') < time)
					&& case_item.appointment_status == 0) {
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
					tooltipStr += 'Phone: ' + vm.splitPhone(patient.home_phone) + '<br/>';
				} if (patient.full_mrn) {
					tooltipStr += 'MRN: ' + patient.full_mrn;
				}

				return tooltipStr;
			};

			vm.splitPhone = function (phone) {
				var result = phone.slice(0, 3) + '-' + phone.slice(3, 6) + '-' + phone.slice(6, 10);
				return result.trim();
			};

			vm.generateReport = function (caseItem) {
				var surgeon_id = $rootScope.loggedUser.id;
				if($scope.listVm.user_id) {
					surgeon_id = $scope.listVm.user_id;
				}

				$http.get('/operative-reports/ajax/' + $scope.org_id + '/generateNonSurgeonReport/' + caseItem.id, {params: {surgeon_id: surgeon_id}}).then(function (result) {
					$window.location = '/operative-reports/my/' + $scope.org_id + '/view/' + result.data;
				});
			};

			vm.isReportExistForCase = function (reports, caseItem) {
				var find = $filter('filter')(reports, {case_id: caseItem.id});
				return !!find.length;
			};

			vm.search();

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

		}]);

})(opakeApp, angular);
