(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive("selectFile", function() {
		return {
			restrict: "EA",
			scope: {
				onSelect: '&'
			},
			link: function($scope, element, attrs) {
				element.addClass('select-file-button');
				var input = element.find('input[type="file"]');
				element.on('click', function(e) {
					e.stopPropagation();
					input.val('');
					input.click();
				});
				input.on('click', function(e) {
					e.stopPropagation();
				});
				input.on('change', function(e) {
					if ($scope.onSelect) {
						$scope.onSelect({
							files: input[0].files
						});
					}
				});
			}
		}
	});

})(opakeApp, angular);
