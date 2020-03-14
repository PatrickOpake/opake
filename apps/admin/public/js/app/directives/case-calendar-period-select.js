(function (opakeApp, angular) {
	'use strict';
	opakeApp.directive('caseCalendarPeriodSelect',
		['$timeout', 'uiCalendarConfig', 'ViewState', 'View', 'CaseCalendarService',
		function ($timeout, uiCalendarConfig, ViewState, View, CaseCalendarService) {
			return {
				restrict: "EC",
				replace: true,
				bindToController: {
					listVm: "=ctrl",
					calendar: "=calendar",
					isIpadCalendar: "=isIpadCalendar"
				},
				controller: function ($scope, $http) {
					var vm = this;

					var calendar = null;

					vm.datepickerOtions = {};
					vm.datepickerOpen = false;
					vm.alertEvents = [];

					vm.items = [
						{
							key: 'day',
							title: 'Day'
						},
						{
							key: 'week',
							title: 'Week'
						}
					];

					if (vm.calendar) {
						vm.items.push({key: 'month', title: 'Month'});
					}

					vm.changeView = function (item) {
						var getDayClass = function (data) {
							var date = data.date,
								mode = data.mode;
							if (mode === 'day') {
								var dayToCheck = new Date(date).setHours(0,0,0,0);
								for (var i = 0; i < vm.alertEvents.length; i++) {
									var currentDay = new Date(vm.alertEvents[i].date).setHours(0,0,0,0);
									if (dayToCheck === currentDay) {
										return 'alerts-exist';
									}
								}
							}
							return '';
						};

						vm.selected_key = item.key;
						vm.datepickerOpen = true;
						vm.datepickerOtions = {
							showWeeks: false,
							customClass: getDayClass
						};

						if (vm.calendar) {
							var viewType = item.key;
							if (viewType === 'day' || viewType === 'week') {
								viewType = ('agenda' + viewType.charAt(0).toUpperCase() + viewType.slice(1));
							}
							CaseCalendarService.get().then(function(calendar) {
								calendar.fullCalendar('changeView', viewType);
								vm.date = calendar.fullCalendar('getDate').toDate();
							});
							if (viewType === 'month') {
								vm.datepickerOtions.datepickerMode = 'month';
								vm.datepickerOtions.minMode = 'month';
							}
							ViewState.update('cases_view_type', item.key);
						}

						if (vm.listVm) {
							vm.date = vm.listVm.date;
							vm.listVm.viewType = item.key;
							ViewState.update('cases_view_type', item.key).then(function () {
								vm.listVm.search();
							});
						}
						if(vm.date) {
							getMonthAlerts(vm.date);
						}
					};

					vm.changeDate = function () {
						if (vm.selected_key === 'week') {
							vm.date = moment(vm.date).startOf('week').toDate();
						} else if (vm.selected_key === 'month') {
							vm.date = moment(vm.date).startOf('month').toDate();
						}

						if (vm.calendar) {
							CaseCalendarService.get().then(function(calendar) {
								calendar.fullCalendar('gotoDate', vm.date);
								$scope.$emit('CaseCalendar.DateChanged');
							});
						}
						if (vm.listVm) {
							vm.listVm.updateDate(vm.date);
							vm.listVm.search();
						}
					};

					$timeout(function () {
						if (vm.calendar) {
							CaseCalendarService.get().then(function(calendar) {
								vm.selected_key = calendar.fullCalendar('getView').intervalUnit;
							});
						}
					});

					$scope.$on('datepicker.monthChanged', function (event, date) {
						getMonthAlerts(date);
					});

					function getMonthAlerts(date) {
						var params = {};
						params.start = moment(date).startOf('month').format('YYYY-MM-DD');
						params.end = moment(date).endOf('month').format('YYYY-MM-DD');
						$http.get('/cases/ajax/' + $scope.org_id + '/searchMonthAlerts/', {params: params }).then(function (response) {
							vm.alertEvents = response.data;
							$scope.$broadcast('refreshDatepickers');
						});
					}
				},
				controllerAs: 'ctrl',
				template: '<div uib-dropdown>' +
					'<button type="button" class="btn" ng-class="{active: ctrl.datepickerOpen}" uib-dropdown-toggle><i class="icon-calendar-gray" ng-class="{\'tablet\' : view.isTablet()}"></i></button>' +
					'<div class="calendar-views" ng-if="ctrl.isIpadCalendar" uib-dropdown-toggle>Calendar Views</div>' +
					'<ul uib-dropdown-menu>' +
					'<li ng-repeat="item in ctrl.items" ng-class="{selected: item.key === ctrl.selected_key}"><a href="" ng-click="changeView(item);"><i class="icon-calendar-gray" ng-class="{\'tablet\' : view.isTablet()}"></i><span class="text-select">{{::item.title}}</span></a></li>' +
					'</ul>' +
					'<uib-datepicker-popup ng-model="ctrl.date" is-open="ctrl.datepickerOpen" datepicker-options="ctrl.datepickerOtions" ng-change="ctrl.changeDate()"></uib-datepicker-popup>' +
					'</div>',
				link: function (scope, elem, attrs, ctrl) {

					scope.changeView = function (item) {
						ctrl.changeView(item);

						if (item.key === 'week') {
							$timeout(function () {
								var popup = elem.find('.uib-datepicker-popup');
								popup.addClass('week-select');
								var unreg = scope.$watch(function () {
									return popup.find('.uib-daypicker').attr('aria-activedescendant');
								}, function (newValue, oldValue) {
									if (!newValue) {
										unreg();
									}
									$timeout(function () {
										angular.forEach(popup.find('.active, .btn-info'), function (activeDay) {
											activeDay = $(activeDay);
											var activeWeek = activeDay.closest('.uib-weeks');
											activeWeek.find('button').attr('class', activeDay.attr('class'));
											activeWeek.find('span').attr('class', activeDay.find('span').attr('class'));
										});
									});
								});
							});
						}

						var popup = elem.find('.uib-datepicker-popup');
						if (View.isTablet()) {
							popup.attr('popup-placement', 'bottom-right');
						} else {
							popup.attr('popup-placement', 'auto');
						}
					};
				}
			};
		}]);

})(opakeApp, angular);
