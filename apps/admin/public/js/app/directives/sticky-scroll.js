(function (opakeApp, angular) {
	'use strict';

	// TODO: при необходимости добавить обновление на watch скопа с проверкой изменения ширины
	opakeApp.directive('stickyScroll', ['$timeout', '$document', '$window', function ($timeout, $document, $window) {
			return {
				restrict: 'A',
				link: function ($scope, elem, attrs, model) {
					$timeout(function () {

						var scrollbar = null;
						var showScroll = function () {
							if (elem[0].scrollWidth <= elem[0].clientWidth) {
								if (scrollbar) {
									scrollbar.remove();
									scrollbar = null;
								}
								return;
							}
							if ($document[0].body.offsetHeight - elem[0].getBoundingClientRect().top < elem[0].offsetHeight) {
								if (scrollbar === null) {
									var inner = angular.element('<div style="height:1px;visibility:hidden;width:' + elem[0].scrollWidth + 'px"></div>');
									scrollbar = angular.element('<div style="position:fixed;width:100%;bottom:0;overflow-y:hidden;overflow-x:scroll;z-index:1000;"></div>');
									scrollbar.append(inner);
									scrollbar.scroll(function () {
										inner[0].style.width = elem[0].scrollWidth + "px";
										elem[0].scrollLeft = scrollbar[0].scrollLeft;
									});
									$timeout(function () {
										if(scrollbar && angular.isDefined(scrollbar[0])) {
											scrollbar[0].scrollLeft = elem[0].scrollLeft;
										}
									});
									elem.append(scrollbar);
								}
							} else if (scrollbar) {
								scrollbar.remove();
								scrollbar = null;
							}
						};

						elem.addClass('has-sticky-scroll');
						$document.scroll(showScroll);
						angular.element($window).resize(showScroll);
						$scope.$watch(function() {
							showScroll();
						});
						showScroll();
					});
				}
			};
		}]);

})(opakeApp, angular);
