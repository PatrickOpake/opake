// Menu
(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('topMenu', ['$rootScope', '$timeout', 'BeforeUnload', function ($rootScope, $timeout, BeforeUnload) {
			return {
				restrict: "EC",
				link: function (scope, elem, attrs, ctrl) {

					if (attrs.menuKey && attrs.activeMenuKey) {

						var activeMenuKey = attrs.activeMenuKey;

						$rootScope.$watch(attrs.menuKey, function (newValue, oldValue) {
							if (newValue) {
								var menu = '';
								angular.forEach(newValue, function (item, key) {
									menu += '<li><a href=' + key + '><span>' + item + '</span></a></li>';
								});
								if (menu) {
									menu = '<ul>' + menu + '</ul>';
								}
								elem.html(menu);

								if ($rootScope[activeMenuKey]) {
									elem.find('li').removeClass('active');
									elem.find('[href="' + $rootScope[activeMenuKey] + '"]').parent().addClass('active');
								}

								elem.find('a').click(function (e) {
									var this_a = this;
									var applyTopMenu = function () {
										var item = angular.element(this_a);
										elem.find('li').removeClass('active');
										item.parent().addClass('active');

										$timeout(function () {
											$rootScope[activeMenuKey] = item.attr('href');
										});
									};

									if (!$rootScope.topMenuCheckFormExcludes || $rootScope.topMenuCheckFormExcludes.indexOf($rootScope[activeMenuKey]) === -1) {
										BeforeUnload.checkForm(applyTopMenu);
									} else {
										applyTopMenu();
									}

									return false;
								});
							}
						});

						$rootScope.$watch(activeMenuKey, function (newValue, oldValue) {
							if (newValue && !angular.equals(newValue, oldValue)) {
								elem.find('li').removeClass('active');
								elem.find('[href="' + newValue + '"]').parent().addClass('active');
							}
						});
					}

				}
			};
		}]);

})(opakeApp, angular);
