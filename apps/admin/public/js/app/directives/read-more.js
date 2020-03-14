(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('readMore', ['$timeout', function ($timeout) {
			return {
				restrict: 'A',
				link: function (scope, elem, attr) {

					$timeout(function () {
						var limit = angular.isUndefined(attr.limit) ? 20 : scope.limit,
							orig = elem.text().trim();

						if (orig.length > (limit + 5)) {
							var text = orig.slice(0, limit) + '...';
							elem.text(text);

							elem.hover(
								function () {
									elem.hide().text(orig).fadeIn('fast');
									elem.css({display: 'inline-block'});
								}, function () {
								elem.text(text);
								elem.css({});
							}
							);
						}
					});
				}
			};
		}]);

})(opakeApp, angular);
