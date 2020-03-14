(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('prefCardActualUse', [function () {
		return {
			restrict: "AC",
			replace: true,
			link: function (scope, element, attrs) {
				element.keydown(function(event) {
					if (event.keyCode === 13) {
						if (element.parent().next().length) {
							element.parent().next().find('.actual-use input').focus();
						} else {
							element.parent().parent().parent().next().find('.pref-card-items .row:first .actual-use input').focus();
						}
					}
				});
			}
		};
	}]);

})(opakeCore, angular);