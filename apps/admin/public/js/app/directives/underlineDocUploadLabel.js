(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('underlineDocUploadLabel', [function () {
		return {
			restrict: "AC",
			replace: true,

			link: function (scope, elem) {
				var uploadIcon = elem.find('.uploaded-file a');
				var label = elem.find("label");

				uploadIcon.hover(function() {
					label.addClass('underlined');
				}, function() {
					label.removeClass('underlined');
				});
			}
		};
	}]);

})(opakeCore, angular);