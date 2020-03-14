(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('caseListSideCalendar', [
		'$rootScope',
		'$timeout',
		'ViewState',
		function ($rootScope, $timeout, ViewState) {
			return {
				restrict: "E",
				replace: true,
				controller: function ($scope) {
					var vm = this;

					vm.date = ViewState.getCasesViewDate();
					vm.options = {
						showWeeks: false
					};

					vm.changeDate = function () {
						$scope.$emit('caseList.calendarDateChanged', vm.date);
					};

					$rootScope.$on('caseList.loadCases', function(e, options) {
						if (!options.isSideCalendarChanged) {
							vm.date = options.date;
							$scope.changeViewType(options.viewType);
						}

					});

				},
				controllerAs: 'sideCalendarVm',
				template: '<div class="side-calendar case-overview-calendar">' +
				'<uib-datepicker ng-model="sideCalendarVm.date" datepicker-options="sideCalendarVm.options" ng-change="sideCalendarVm.changeDate()">' +
				'</uib-datepicker>' +
				'</div>',
				link: function (scope, elem) {
					scope.changeViewType = function (viewType) {
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
