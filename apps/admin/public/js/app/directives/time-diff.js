(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('timeDiff', [function () {
		return {
			restrict: "EA",
			scope: {
				start: "=",
				end: "="
			},
			link: function (scope, element, attrs) {
				var diff = null;

				scope.$watch(function () {
					return {start: scope.start, end: scope.end};
				}, function (newVal, oldVal) {

					var endChanged = newVal.end.getTime() !== oldVal.end.getTime();
					if (endChanged || !diff) {
						var end = moment(newVal.end),
							start = moment(newVal.start),
							time_diff = end.diff(start, 'minutes');

						if (time_diff <= 0) {
							scope.end = oldVal.end;
						} else if (!end.isSame(newVal.start, "day")) {
							scope.end = moment(newVal.start).set({hour: 23, minute: 59}).toDate();
						} else {
							diff = time_diff;
						}
					}
					if (newVal.start.getTime() !== oldVal.start.getTime() && !endChanged) {
						var end = moment(newVal.start).add(diff, 'minutes');
						if(!end.isSame(newVal.start, "day")) {
							end = moment(newVal.start).set({hour: 23, minute: 59});
						}
						scope.end = end.toDate();
					}
				}, true);
			}
		};
	}]);

	opakeApp.directive('datetimeDiff', [function () {
		return {
			restrict: "EA",
			scope: {
				start: "=",
				end: "="
			},
			link: function (scope, element, attrs) {
				scope.$watch(function () {
					return {start: scope.start, end: scope.end};
				}, function (newVal, oldVal) {
					if (moment(newVal.end).diff(newVal.start, 'minutes') <= 0) {
						scope.end = moment(newVal.start).add(1, 'hours').toDate();
					}
				}, true);
			}
		};
	}]);

})(opakeApp, angular);
