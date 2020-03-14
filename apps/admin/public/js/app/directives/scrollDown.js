(function (opakeApp, angular) {
	'use strict';

	function bindingState() {
		var activated = true;
		return {
			getValue: function() {
				return activated;
			},
			setValue: function(value) {
				activated = value;
			}
		};
	}

	opakeApp.directive('scrollDown', ['$parse', '$window', '$timeout', function ($parse, $window, $timeout) {
		return {
			priority: 1,
			restrict: "AC",
			replace: true,

			link: function (scope, $element, attrs) {
				var element = $element[0],
					activationState = bindingState();
				
				var direction = {
					isAttached: function(element) {
						return element.scrollTop + element.clientHeight + 1 >= element.scrollHeight;
					},
					scroll: function(element) {
						element.scrollTop = element.scrollHeight;
					}
				};

				function scrollIfGlued() {
					if (activationState.getValue() && !direction.isAttached(element)) {
						direction.scroll(element);
					}
				}

				function onScroll() {
					activationState.setValue(direction.isAttached(element));
				}

				scope.$watch(scrollIfGlued);

				$timeout(scrollIfGlued, 0, false);

				$window.addEventListener('resize', scrollIfGlued, false);

				$element.bind('scroll', onScroll);
			}
		};
	}]);

})(opakeApp, angular);