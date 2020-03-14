// List of cases
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseListCrtl', [
		'$scope',
		'$rootScope',
		'$q',
		'$httpParamSerializer',
		'Source',
		'uiCalendarConfig',
		'Calendar',
		'Cases',
		'Case',

		function ($scope, $rootScope, $q, $httpParamSerializer, Source, uiCalendarConfig, Calendar, Cases, Case) {

			var vm = this;

			$scope.cases = Cases;
			$scope.calendar = Calendar;

			window.$scope = $scope;

			vm.search_params = {};
			vm.cases_q = {
				type: 'GET',
				url: '/cases/ajax/' + $scope.org_id + '/search/',
				data: []
			};
			vm.blocks_q = {
				type: 'GET',
				url: '/cases/ajax/blocking/' + $scope.org_id + '/search/',
				data: []
			};

			vm.in_service_q = {
				type: 'GET',
				url: '/cases/ajax/' + $scope.org_id + '/searchInService/',
				data: []
			};

			vm.cases_src = [vm.cases_q, vm.blocks_q, vm.in_service_q];

			vm.surgeons_src = {
				type: 'GET',
				url: '/cases/ajax/' + $scope.org_id + '/getAllSurgeons/',
				data: []
			};

			vm.showMoreActions = false;
			vm.showAlerts = false;

			$rootScope.$on('surgeonsCasesHidden', function(event, hiddenSurgeonIds) {
				vm.search(hiddenSurgeonIds);
			});

			$rootScope.$on('CaseCalendar.DateChanged', function() {
				vm.showMoreActions = false;
			});

			vm.init = function () {
				Source.getLocations().then(function (result) {
					vm.rooms_full_list = result;
					vm.loaded = true;
				});
			};

			vm.search = function (hiddenSurgeonIds) {
				angular.forEach(vm.search_params, function (v, k) {
					vm.cases_q.data[k] = v;
				});
				angular.forEach(vm.search_params, function (v, k) {
					vm.blocks_q.data[k] = v;
				});
				angular.forEach(vm.search_params, function (v, k) {
					vm.in_service_q.data[k] = v;
				});

				vm.blocks_q.data['ignore_blocks'] =
					!!(vm.search_params.id || vm.search_params.patient || vm.search_params.procedure);

				if (hiddenSurgeonIds && angular.isArray(hiddenSurgeonIds)) {
					vm.cases_q.data['hidden_surgeon_ids'] = JSON.stringify(hiddenSurgeonIds);
				}

				if(vm.cases_q.data['alert']) {
					vm.cases_q.data['alert'] = JSON.stringify(vm.cases_q.data['alert']);
				}

				angular.forEach(vm.search_params, function (v, k) {
					vm.surgeons_src.data[k] = v;
				});

				Calendar.refetchEvents();
			};

			vm.reset = function () {
				for (var key in vm.cases_q.data) {
					delete vm.cases_q.data[key];
				}
				for (var key in vm.blocks_q.data) {
					delete vm.blocks_q.data[key];
				}
				for (var key in vm.in_service_q.data) {
					delete vm.in_service_q.data[key];
				}
				for (var key in vm.surgeons_src.data) {
					delete vm.surgeons_src.data[key];
				}
				for (var key in vm.search_params) {
					delete vm.search_params[key];
				}
				vm.search();
			};

			vm.isDayView = function () {
				return uiCalendarConfig.calendars['case-calendar'].fullCalendar('getView').intervalUnit === "day";
			};

			vm.createCase = function () {
				var newCase = new Case();
				if (vm.isDayView()) {
					var date = uiCalendarConfig.calendars['case-calendar'].fullCalendar('getDate').toDate();
					newCase.time_start.setFullYear(date.getFullYear(), date.getMonth(), date.getDate());
					newCase.time_end.setFullYear(date.getFullYear(), date.getMonth(), date.getDate());
				}
				Calendar.createCase(newCase);
			};

			vm.createInService = function () {
				Calendar.showInServiceForm();
			};

			vm.createBooking = function () {
				window.location = '/booking/' + $scope.org_id + '/create';
			};

			vm.getPrintUrl = function () {
				var params = {};
				if(uiCalendarConfig.calendars['case-calendar']) {
					var date = uiCalendarConfig.calendars['case-calendar'].fullCalendar('getDate');
					var currentDate = new Date();
					var weeks = moment(currentDate).diff(date, 'weeks');
					var intervalUnit = uiCalendarConfig.calendars['case-calendar'].fullCalendar('getView').intervalUnit;
					if(intervalUnit === 'day') {
						params.dos = moment(date).format('YYYY-MM-DD');
						params.start_of_week = getStartOfWeek(date);
						params.end_of_week = getEndOfWeek(date);
					} else 	if(intervalUnit === 'week') {
						params.start_of_week = getStartOfWeek(date);
						params.end_of_week = getEndOfWeek(date);
						params.dos = moment(currentDate).subtract(weeks, 'weeks').format('YYYY-MM-DD');
					} else 	if(intervalUnit === 'month') {
						params.start = moment(date).startOf('month').format('YYYY-MM-DD');
						params.end = moment(date).endOf('month').format('YYYY-MM-DD');
					}

					params.view_type = intervalUnit;

					return '/cases/ajax/' + $scope.org_id + '/exportOverview/?' + $httpParamSerializer(params) + '&to_download=false';

				}
			};

			vm.deactivateWaitingRoomMode = function () {
				$scope.$emit('CalendarWaitingRoomDeactivated');
			};

			var getStartOfWeek = function (date) {
				return moment(date).startOf('week').format('YYYY-MM-DD');
			};

			var getEndOfWeek = function (date) {
				return moment(date).endOf('week').format('YYYY-MM-DD');
			};


		}]);

})(opakeApp, angular);
