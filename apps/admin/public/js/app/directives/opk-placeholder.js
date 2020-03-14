(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('opkPlaceholder', [function () {
		return {
			restrict: "A",
			require: 'ngModel',
			scope: {
				placeholder: "@opkPlaceholder"
			},
			link: function (scope, element, attrs, ctrl) {
				scope.$watch('placeholder', function(){
					if(scope.placeholder) {
						element[0].placeholder = scope.placeholder;
					} else {
						element[0].placeholder = '';
					}
				});

				element.bind('click', function(){
					element[0].placeholder = '';
					if (scope.placeholder) {
						ctrl.$setViewValue(scope.placeholder);
						ctrl.$render();
					}
				});
			}
		};
	}]);

})(opakeApp, angular);
