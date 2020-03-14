(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('submitByEnter', ['$timeout', function ($timeout) {
		return {
			restrict: "A",
			scope: {
				submitByEnter: '&'
			},
			link: function (scope, element, attrs) {
				$timeout(function () {
					element.find('input').on("keydown", function(event) {
						if (event.keyCode === 13) {
							if (angular.isFunction(scope.submitByEnter)) {
								scope.$apply(function() {
									scope.submitByEnter({
										event: event
									});
								});
							}
							event.preventDefault();
						}
					});
				});
			}
		};
	}]);

})(opakeApp, angular);
