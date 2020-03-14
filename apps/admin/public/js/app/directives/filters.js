(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('filtersPanel', [
			'$timeout',
			function ($timeout) {
				return {
					restrict: "E",
					transclude: true,
					scope: true,
					bindToController: {
						listVm: "=ctrl"
					},
					controller: function ($scope) {
						var vm = this;

						vm.submit = function () {
							if (vm.listVm) {
								$timeout(function () {
									vm.listVm.search_params.p = 0;
									$scope.$eval(vm.listVm.search(true));
								});
							}
						};

						vm.clear = function () {
							if (vm.listVm) {
								$scope.$eval(vm.listVm.reset);
							}
						};

					},
					controllerAs: 'flt',
					template: '<div class="filters-panel">' +
							'<div class="filters-panel--content"></div>' +
							'<div ng-if="!isHideButtons" class="filters-panel--actions">' +
								'<a href="" ng-click="flt.submit()" class="btn btn-success">{{nameSearchBtn}}</a>' +
								'<a href="" ng-click="flt.clear()" class="reset">clear</a>' +
							'</div>' +
						'</div>',
					link: function (scope, elem, attrs, ctrl, transclude) {
						scope.nameSearchBtn = attrs.nameSearchBtn || 'Search';
						scope.isHideButtons = false;

						if(attrs.isHideButtons) {
							scope.$watch(attrs.isHideButtons, function(value) {
								scope.isHideButtons = value;
							});
						}

						transclude(scope, function (clone, scope) {
							elem.find(".filters-panel--content").append(clone);
						});

						$timeout(function () {
							elem.find('input').on('keydown', function (e) {
								var self = $(this);
								if (e.keyCode === 13) {
									if (!$(this).closest('.opk-select').length) {
										self.data('changeHandled', true);
										self.data('changeHandledValue', self.val());
										ctrl.submit();
									}
								}
							});
							elem.find('select, input').on('change', function (e) {

								if (!$(this).closest('.opk-select').length) {
									var self = $(this);
									if (self.data('changeHandled')) {
										if (self.val() === self.data('changeHandledValue')) {
											self.data('changeHandled', false);
											self.data('changeHandledValue', '');
											return;
										}
									}
									ctrl.submit();
								}

							});
							angular.forEach(elem.find('.opk-select'), function (item) {
								angular.element(item).scope().$on("select", function (e, events) {
									ctrl.submit();
								});
							});
						});
					}
				};
			}]
		);

		opakeApp.directive('filtersPanelWithParams', ['$window', '$timeout', function ($window, $timeout) {
			return {
				restrict: "E",
				transclude: true,
				scope: true,
				controller: function ($scope) {
					var vm = this;

					vm.params = {};

					vm.submit = function () {
						$timeout(function () {
							var loc = '';
							angular.forEach(vm.params, function (val, key) {
								loc = updateParam(key, val, loc);
							});
							loc = updateParam('p', '', loc);
							$window.location = loc;
						});
					};

					vm.clear = function () {
						if (vm.listVm) {
							$scope.$eval(vm.listVm.reset);
						} else {
							var params = {};
							angular.forEach(vm.params, function (val, key) {
								params[key] = "";
							});
							vm.params = params;
							vm.submit();
						}
					};
				},
				controllerAs: 'flt',
				template: '<div class="filters-panel">' +
						'<div class="filters-panel--content"></div>' +
						'<div class="filters-panel--actions">' +
							'<a href="" ng-click="flt.submit()" class="btn btn-success">Search</a>' +
							'<a href="" ng-click="flt.clear()" class="reset">clear</a>' +
						'</div>' +
					'</div>',
				link: function (scope, elem, attrs, ctrl, transclude) {

					ctrl.params = attrs.params ? angular.fromJson(attrs.params) : {};

					transclude(scope, function (clone, scope) {
						elem.find(".filters-panel--content").append(clone);
					});

					$timeout(function () {
						elem.find('input').on('keydown', function (e) {
							var self = $(this);
							if (e.keyCode === 13) {
								if (!$(this).closest('.opk-select').length) {
									self.data('changeHandled', true);
									self.data('changeHandledValue', self.val());
									ctrl.submit();
								}
							}
						});
						elem.find('select, input').on('change', function (e) {
							if (!$(this).closest('.opk-select').length) {
								var self = $(this);
								if (self.data('changeHandled')) {
									if (self.val() === self.data('changeHandledValue')) {
										self.data('changeHandled', false);
										self.data('changeHandledValue', '');
										return;
									}
								}

								ctrl.submit();
							}
						});
						angular.forEach(elem.find('.opk-select'), function (item) {
							angular.element(item).scope().$on("select", function (e, events) {
								ctrl.submit();
							});
						});
					});

				}
			};
		}]);

})(opakeApp, angular);
