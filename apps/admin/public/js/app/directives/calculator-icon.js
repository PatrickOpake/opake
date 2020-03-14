(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('calculatorIcon', ['$rootScope', function ($rootScope) {
			return {
				restrict: "AC",
				replace: true,
				link: function (scope, elem) {
					$rootScope.showCalculatorWidget = false;
					elem.on('click', function() {
						$rootScope.$apply(function () {
							$rootScope.showCalculatorWidget = !$rootScope.showCalculatorWidget;
						});
					});
				}
			};
		}]);

})(opakeApp, angular);
