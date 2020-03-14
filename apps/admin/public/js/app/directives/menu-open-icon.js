(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('menuOpenIcon', ['$rootScope', 'View', function ($rootScope, View) {
			return {
				restrict: "E",
				replace: true,
				template: '<a class="navbar-open-menu" href="#">'+
						'<i class="{{iconClass}}"></i>' +
					'</a>',
				link: function (scope, elem) {
					if (View.isTablet()) {
						elem.show();
					}
					scope.iconClass = 'icon-hamburger-white';
					$rootScope.showLeftMenu = false;
					elem.on('click', function() {
						$rootScope.$apply(function () {
							$rootScope.showLeftMenu = !$rootScope.showLeftMenu;
							setMenuIconClass();
						});
					});
					$rootScope.$on('Screen.resize', function() {
						if (View.isTablet()) {
							elem.show();
						} else {
							$rootScope.showLeftMenu = false;
							elem.hide();
						}
						setMenuIconClass();
					});

					function setMenuIconClass() {
						if ($rootScope.showLeftMenu) {
							scope.iconClass = 'icon-x-close-white';
						} else {
							scope.iconClass = 'icon-hamburger-white';
						}
					}
				}
			};
		}]);

})(opakeApp, angular);
