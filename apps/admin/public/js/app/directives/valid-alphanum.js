(function (opakeApp, angular) {
	'use strict';
	opakeApp.directive('validAlphanum', function () {
		return {
			require: '?ngModel',
			link: function(scope, element, attr, ngModelCtrl) {
				function fromUser(text) {
					var transformedInput = text.replace(/[^0-9a-zA-Z\-\.]/g, '');
					if (transformedInput !== text) {
						ngModelCtrl.$setViewValue(transformedInput);
						ngModelCtrl.$render();
					}
					return transformedInput; // or return Number(transformedInput)
				}
				ngModelCtrl.$parsers.push(fromUser);
			}
		};
	});
})(opakeApp, angular);
