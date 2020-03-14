(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('opkSrc', [function () {
		return {
			restrict: 'A',
			link: function (scope, element, attrs) {
				var imageSrc = attrs.opkSrc;
				var errorSrc = attrs.opkSrcError;
				var preloadSrc = attrs.opkSrcPreload;

				element.css('visibility', 'hidden');

				element.on('load', function() {
					element.css('visibility', 'visible');
				});

				if (preloadSrc) {
					element.attr('src', preloadSrc);
				}

				var img = $('<img>');
				img.on('load', function() {
					element.attr('src', imageSrc);
				});
				img.on('error', function() {
					if (errorSrc) {
						element.attr('src', errorSrc);
					}
				});
				img.attr('src', imageSrc);
			}
		};
	}]);

})(opakeCore, angular);
