(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('sideCalendar', [
		'$window',
		'$location',
		'$timeout',
		'$rootScope',
		'uiCalendarConfig',
		'ViewState',
		function ($window, $location, $timeout, $rootScope, uiCalendarConfig, ViewState) {
			return {
				restrict: "E",
				replace: true,
				controller: function ($scope) {
					var vm = this;

					vm.date = ViewState.getCasesViewDate();
					vm.options = {
						showWeeks: false
					};

					$scope.$on('CaseCalendarLoaded', function (event, view) {
						var localDate = view.intervalStart.toDate();
						vm.date = new Date(localDate.valueOf() + localDate.getTimezoneOffset() * 60000);
						$scope.changeViewType(view.intervalUnit);
					});

					vm.changeDate = function () {
						var calendar = getCaseCalendar();
						if (calendar) {
							$scope.$emit('calendarDateChanged', vm.date);
						} else {
							$window.location = '/cases/' + $rootScope.org_id + '#?date=' + moment(vm.date).format('YYYY-MM-DD');
						}
					};

					var params = $location.search();
					if (params.date) {
						var date = moment(params.date);
						if (date.isValid()) {
							vm.date = date.toDate();
						}
					}

					function getCaseCalendar() {
						return uiCalendarConfig.calendars['case-calendar'];
					}
				},
				controllerAs: 'sideCalendarVm',
				template: '<div class="side-calendar">' +
				'<uib-datepicker ng-model="sideCalendarVm.date" datepicker-options="sideCalendarVm.options" ng-change="sideCalendarVm.changeDate()">' +
				'</uib-datepicker>' +
				'</div>',
				link: function (scope, elem) {
					scope.changeViewType = function (viewType) {
						if (viewType === 'month') {
							$timeout(function () {
								var activeButtons = elem.find('.uib-daypicker tbody td .btn-default');
								activeButtons.addClass('active');
							});
						}
						if (viewType === 'week') {
							$timeout(function () {
								var activeButton = elem.find('.uib-daypicker tbody td .active');
								var activeRow = activeButton.parent().parent();
								var activeButtons = activeRow.find('td .btn-default');
								activeButtons.addClass('active');
							});
						}
					};
				}
			};
		}]);

})(opakeCore, angular);
