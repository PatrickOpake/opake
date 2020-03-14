(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('timeLength', ['$filter', '$timeout', function ($filter, $timeout) {
			return {
				restrict: "EA",
				replace: true,
				scope: true,
				bindToController: {
					start: "=",
					end: "=",
					hoursStep: '=?',
					minutesStep: '=?'
				},
				controller: function ($scope) {

					var vm = this;

					vm.hoursStep = vm.hoursStep || 1;
					vm.minutesStep = vm.minutesStep || 1;

					vm.hoursOptions = range(0, 12, vm.hoursStep).map(String);
					vm.minutesOptions = range(0, 55, vm.minutesStep).map(String);

					vm.updateEnd = function () {
						if (angular.isDefined(vm.hours) && angular.isDefined(vm.minutes)) {
							var end = moment(vm.start).add(vm.hours, 'hours').add(vm.minutes, 'minutes');
							if (!end.isSame(vm.start, "day")) {
								end.set({hour: 23, minute: (60 - vm.minutesStep)});
							}
							vm.end = end.toDate();
						} else {
							setLengthFromDates();
						}
					};

					$scope.$watch('tlVm.start', function (newVal, oldVal) {
						vm.updateEnd();
					}, true);

					$scope.$watch('tlVm.end', function (newVal, oldVal) {
						setLengthFromDates();
					}, true);


					function setLengthFromDates() {
						if (angular.isDate(vm.end) && angular.isDate(vm.start)) {
							var duration = moment.duration(moment(vm.end).diff(moment(vm.start)));
							vm.hours = '' + duration.get("hours");
							vm.minutes = '' + duration.get("minutes");
						}
					}

					function range(a, b, step) {
						var res = [];
						res[0] = a;
						step = step || 1;
						while (a + step <= b) {
							res[res.length] = a += step;
						}
						return res;
					}

				},
				controllerAs: 'tlVm',
				template: '<div class="time-length">' +
					'<div class="time-length--section"><opk-select class="small" ng-model="tlVm.hours" change="tlVm.updateEnd()" options="item for item in tlVm.hoursOptions" placeholder="Hrs" select-options="{editItem: true, listFilter: \'none\', newItem: \'prompt\', scrollToActiveElement: true, dropdownFilter: \'opkTimeLengthStrictHighlight\'}"></opk-select><span>Hrs</span></div>' +
					'<div class="time-length--section"><opk-select class="small" ng-model="tlVm.minutes" change="tlVm.updateEnd()" options="item for item in tlVm.minutesOptions" placeholder="Mins" ng-change="" select-options="{editItem: true, listFilter: \'none\', newItem: \'prompt\', scrollToActiveElement: true, dropdownFilter: \'opkTimeLengthStrictHighlight\'}"></opk-select><span>Mins</span></div>' +
					'</div>'
			};
		}]);


	opakeApp.filter('opkTimeLengthStrictHighlight', ['$sce', 'oiSelectEscape', function($sce, oiSelectEscape) {
		return function(label, query) {
			var html;

			if (query.length > 0 || angular.isNumber(query)) {
				label = label.toString();
				query = oiSelectEscape(query.toString());


				if (query === label) {
					html = label.replace(query, '<strong class="current-active-label">$&</strong>');
				} else {
					html = label;
				}

			} else {
				html = label;
			}

			return $sce.trustAsHtml(html);
		};
	}])

})(opakeApp, angular);
