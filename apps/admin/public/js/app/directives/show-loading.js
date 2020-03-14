(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('showLoading', [function () {
		return {
			restrict: "A",
			link: function (scope, element, attrs) {

				var waitingLayer = $(
					'<div class="waiting-layer">' +
					'</div>'
				);

				if(!attrs.withoutSpinner) {
					waitingLayer.append('<div class="loading-spinner"></div>');
				}

				element.addClass('show-loading-container');
				element.append(waitingLayer);

				scope.$watch(attrs.showLoading, function(value) {
					if (!value) {
						waitingLayer.hide();
					} else {
						waitingLayer.show();
					}
				});
			}
		};
	}]);

	opakeApp.directive('showLoadingList', [function () {
		return {
			restrict: "A",
			link: function (scope, element, attrs) {

				var waitingLayer = angular.element(
					'<div class="waiting-layer-list"></div>'
				);

				element.append(waitingLayer);

				scope.$watch(attrs.showLoadingList, function(value) {
					if (!value) {
						waitingLayer.hide();
						waitingLayer.siblings().show();
					} else {
						waitingLayer.show();
						waitingLayer.siblings().hide();
					}
				});
			}
		};
	}]);

})(opakeApp, angular);
