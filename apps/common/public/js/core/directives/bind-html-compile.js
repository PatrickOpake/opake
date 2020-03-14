(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('bindHtmlCompile', ['$compile', '$timeout', function ($compile, $timeout) {
			return {
				restrict: 'A',
				link: function (scope, element, attrs) {
					scope.$watch(function () {
						var val = scope.$eval(attrs.bindHtmlCompile);
						if (!angular.isString(val)) {
							val = val.toString()
						}
						return val;
					}, function (val) {
						element.html(val);
						$compile(element.contents())(scope);
					});
				}
			};
		}]);

})(opakeCore, angular);
