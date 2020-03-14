(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('nameField', [function () {
		var caretPos = 1;

		function setCaretPosition(elem, caretPos) {
			if (elem !== null) {
				if (elem.createTextRange) {
					var range = elem.createTextRange();
					range.move('character', caretPos);
					range.select();
				} else {
					if (elem.setSelectionRange) {
						elem.focus();
						elem.setSelectionRange(caretPos, caretPos);
					} else
						elem.focus();
				}
			}
		}
		
		return {
			restrict: "AC",
			replace: true,
			scope: {
				model: "=ngModel"
			},

			link: function (scope, element, attrs) {
				scope.$watch("model", function(newValue, oldValue) {
					if (angular.isString(scope.model)) {
						var ch = newValue.charAt(0);
						if (ch && oldValue && ch.toLowerCase() == oldValue.toLowerCase().charAt(1)) {
							return;
						}
						if (ch !== ch.toUpperCase()){
							element.val(scope.model.charAt(0).toUpperCase() + scope.model.slice(1));
							setCaretPosition(element[0], caretPos);
						}
					}
				});

			}
		};
	}]);

})(opakeCore, angular);