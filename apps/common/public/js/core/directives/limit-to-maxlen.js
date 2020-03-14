(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('limitToMaxLen', [function () {
		return {
			restrict: "A",
			require: 'ngModel',
			scope: {
				model: "="
			},
			link: function (scope, element, attrs, ctrl) {
				var limit = parseInt(attrs.ngMaxlength);
				ctrl.$parsers.unshift(function (value) {
					if (value.length > limit) {
						value = ctrl.$modelValue;
						ctrl.$setViewValue(value);
						ctrl.$render();
					}

					return value;
				});
			}
		};
	}]);

})(opakeCore, angular);
