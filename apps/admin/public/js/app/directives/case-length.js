(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('caseLength', ['$filter', '$timeout', function ($filter, $timeout) {
			return {
				restrict: "EA",
				replace: true,
				scope: true,
				bindToController: {
					time: "="
				},
				controller: function ($scope) {
					var vm = this;
					vm.hoursOptions = $filter('range')([], 0, 12).map(String);
					vm.minutesOptions = $filter('range')([], 0, 59).map(String);

					vm.setLengthFromDates = function () {
						if (angular.isDate(vm.time)) {
							var time = moment(vm.time);
							vm.hours = '' + time.get("hours");
							vm.minutes = '' + time.get("minutes");
						} else {
							vm.hours = '' + 0;
							vm.minutes = '' + 0;
						}
					};

					vm.updateTime = function () {
						if (vm.isDateValid()) {
							var time = moment(new Date());
							time.hour(vm.hours);
							time.minute(vm.minutes);
							vm.time = time.toDate();
						} else {
							vm.setLengthFromDates();
						}
					};

					vm.isDateValid = function () {
						return vm.hours && vm.minutes && (vm.hours !== '0' || vm.minutes !== '0');
					};

					$scope.$watch('clVm.time', function (newVal, oldVal) {
						vm.setLengthFromDates();
					}, true);

				},
				controllerAs: 'clVm',
				template: '<div class="time-length">' +
					'<div class="time-length--section"><opk-select class="small" ng-model="clVm.hours" change="clVm.updateTime()" options="item for item in clVm.hoursOptions" placeholder="Hrs" select-options="{editItem: true, listFilter: \'none\', newItem: \'prompt\', scrollToActiveElement: true, dropdownFilter: \'opkTimeLengthStrictHighlight\'}"></opk-select><span>Hrs</span></div>' +
					'<div class="time-length--section"><opk-select class="small" ng-model="clVm.minutes" change="clVm.updateTime()" options="item for item in clVm.minutesOptions" placeholder="Mins" ng-change="" select-options="{editItem: true, listFilter: \'none\', newItem: \'prompt\', scrollToActiveElement: true, dropdownFilter: \'opkTimeLengthStrictHighlight\'}"></opk-select><span>Mins</span></div>' +
					'</div>'
			};
		}]);

})(opakeApp, angular);
