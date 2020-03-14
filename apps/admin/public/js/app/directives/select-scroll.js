(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('selectScroll', ['$timeout', function ($timeout) {
		return {
			restrict: "C",
			require: "uiSelect",
			link: function (scope, elem, attrs, controller) {
				var scrollToBottom = function() {
					var scrollElement = elem.children('div')[0];
					scrollElement.scrollTop = scrollElement.scrollHeight;
				};

				var sizeSearchInput = function() {
					var searchInput = elem.find('input.ui-select-search'),
						input = searchInput[0],
						container = searchInput.parent().parent()[0],
						calculateContainerWidth = function() {
							return container.clientWidth * !!input.offsetParent;
						},
						updateIfVisible = function(containerWidth) {
							if (containerWidth === 0) {
								return false;
							}
							var	selectContainer =  angular.element(container).children('div'),
								lastTag = angular.element(container).find('span.ui-select-match').children().last(),
								inputWidth =  selectContainer.width() - 20;


							if(lastTag.length) {
								var widthTagOffset = lastTag[0].offsetLeft;
							}

							selectContainer.width(containerWidth );

							if (widthTagOffset <= 0) {
								inputWidth = selectContainer.width() - lastTag.width() - 20;
							}

							searchInput.css('width', inputWidth+'px');
							return true;
						};

					$timeout(function() {
						updateIfVisible(calculateContainerWidth());
					});
				};
				$timeout(function() {
					sizeSearchInput();
				});

				controller.onSelectCallback = function() {
					sizeSearchInput();
					scrollToBottom();
				};

				controller.onRemoveCallback = function() {
					sizeSearchInput();
					scrollToBottom();
				};
			}
		};
	}]);

})(opakeApp, angular);
