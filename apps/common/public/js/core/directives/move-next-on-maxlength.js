(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive("moveNextOnMaxlength", function() {
		return {
			restrict: "A",
			link: function(scope, element, attrs) {
				var maxlength = parseInt(attrs.ngMaxlength);
				var inputs = element.parent().find('input');

				element.on("input", function(e) {
					if (element.val().length == maxlength) {
						var currentElement = inputs.index(element);
						var nextElement = inputs[currentElement + 1];
						if (nextElement && nextElement.value == '') {
							nextElement.focus();
						}
					}
				});
			}
		}
	});

})(opakeCore, angular);
