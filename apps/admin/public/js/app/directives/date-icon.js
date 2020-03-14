(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('dateIcon', ['$compile', '$timeout', function ($compile, $timeout) {
			return {
				restrict: "E",
				require: 'ngModel',
				replace: true,
				transclude: true,
				scope: {
					ngModel: "=",
					ngDisabled:'='
				},
				bindToController: {
					listVm: "=ctrl"
				},
				controller: function ($scope) {
					var vm = this;

					vm.search = function () {
						if (vm.listVm) {
							$timeout(function () {
								$scope.$eval(vm.listVm.search(true));
							});
						}
					};

				},
				controllerAs: 'flt',
				template: '<div class="date-icon">' +
						'<a href=""><i class="icon-calendar-gray" ng-model="ngModel" type="button" \n\
							uib-datepicker-popup is-open="open" ng-disabled="ngDisabled"\n\
							datepicker-options="{showWeeks: false}"\n\
							ng-change="flt.search()"></i></a>' +
					'</div>',
				link: function (scope, elem) {
					var icon = elem.find('i');

					scope.open = false;

					icon.click(function (e) {
						scope.$apply(function () {
							scope.open = !scope.open;
						});
						e.preventDefault();
					});
				}
			};
		}]);

})(opakeApp, angular);
