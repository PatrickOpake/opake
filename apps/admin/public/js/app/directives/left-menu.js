(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('leftMenu', ['$rootScope', '$compile', 'View', function ($rootScope, $compile, View) {
			return {
				restrict: "E",
				scope: {
					menuItems: '=items',
					menuType: '=menuType',
					activeMenu: '=activeMenu',
					showParams: '=showParams'
				},
				controller: [
					'$rootScope',
					'$scope',
					'$window',
					'View',
					function ($rootScope, $scope, $window, View) {
						var vm = this;
						vm.menuItems = $scope.menuItems;
						vm.activeMenu = $scope.activeMenu;
						vm.showParams = $scope.showParams;
						vm.menuType = $scope.menuType || 'default';
						vm.status = {};
						vm.templatePath = getCurrentTemplatePath();
						if(vm.menuItems[vm.activeMenu] && vm.menuItems[vm.activeMenu].items) {
							vm.status[vm.activeMenu] = vm.menuItems[vm.activeMenu].items.length !== 0;
						}

						vm.toggleOpen = function (key) {
							if(!vm.menuItems[key].items || vm.menuItems[key].items.length === 0) {
								$window.location = vm.menuItems[key].firstItemLink;
							} else {
								vm.status[key] = !vm.status[key];
							}
						};

						$rootScope.$on('Screen.resize', function() {
							vm.templatePath = getCurrentTemplatePath();
						});

						function getCurrentTemplatePath() {
							var device = 'pc';
							if (View.isTablet()) {
								device = 'tablet';
							}
							return View.get('widgets/left-menu-' + device + '.html');
						}
					}
				],
				controllerAs: 'menuVm',

				template: '<div class="left-menu-widget" ng-include src="menuVm.templatePath"></div>',
				link: function (scope, elem) {

				}
			};
		}]);

})(opakeApp, angular);
