(function (opakeApp, angular) {
	'use strict';

	angular.module("template/timepicker/timepicker.html", []).run(["$templateCache", function ($templateCache) {
			$templateCache.put("template/timepicker/timepicker.html",
				"<table>\n" +
				"  <tbody>\n" +
				"    <tr class=\"text-center\" ng-show=\"::showSpinners\">\n" +
				"      <td><a ng-click=\"incrementHours()\" ng-class=\"{disabled: noIncrementHours()}\" class=\"btn btn-link\" ng-disabled=\"noIncrementHours()\" tabindex=\"{{::tabindex}}\"><span class=\"glyphicon glyphicon-chevron-up\"></span></a></td>\n" +
				"      <td>&nbsp;</td>\n" +
				"      <td><a ng-click=\"incrementMinutes()\" ng-class=\"{disabled: noIncrementMinutes()}\" class=\"btn btn-link\" ng-disabled=\"noIncrementMinutes()\" tabindex=\"{{::tabindex}}\"><span class=\"glyphicon glyphicon-chevron-up\"></span></a></td>\n" +
				"      <td ng-show=\"showMeridian\"><a ng-click=\"toggleMeridian()\" ng-class=\"{disabled: noToggleMeridian()}\" class=\"btn btn-link\" ng-disabled=\"noToggleMeridian()\" tabindex=\"{{::tabindex}}\"><span class=\"glyphicon glyphicon-chevron-up\"></span></a></td>\n" +
				"    </tr>\n" +
				"    <tr>\n" +
				"      <td class=\"form-group\" ng-class=\"{'has-error': invalidHours}\">\n" +
				"        <input style=\"width:46px;\" type=\"text\" ng-model=\"hours\" ng-change=\"updateHours()\" class=\"form-control text-center\" ng-readonly=\"::readonlyInput\" maxlength=\"2\" tabindex=\"{{::tabindex}}\">\n" +
				"      </td>\n" +
				"      <td>:</td>\n" +
				"      <td class=\"form-group\" ng-class=\"{'has-error': invalidMinutes}\">\n" +
				"        <input style=\"width:46px;\" type=\"text\" ng-model=\"minutes\" ng-change=\"updateMinutes()\" class=\"form-control text-center\" ng-readonly=\"::readonlyInput\" maxlength=\"2\" tabindex=\"{{::tabindex}}\">\n" +
				"      </td>\n" +
				"      <td ng-show=\"showMeridian\"><button type=\"button\" ng-class=\"{disabled: noToggleMeridian()}\" class=\"btn btn-default text-center\" ng-click=\"toggleMeridian()\" ng-disabled=\"noToggleMeridian()\" tabindex=\"{{::tabindex}}\">{{meridian}}</button></td>\n" +
				"    </tr>\n" +
				"    <tr class=\"text-center\" ng-show=\"::showSpinners\">\n" +
				"      <td><a ng-click=\"decrementHours()\" ng-class=\"{disabled: noDecrementHours()}\" class=\"btn btn-link\" ng-disabled=\"noDecrementHours()\" tabindex=\"{{::tabindex}}\"><span class=\"glyphicon glyphicon-chevron-down\"></span></a></td>\n" +
				"      <td>&nbsp;</td>\n" +
				"      <td><a ng-click=\"decrementMinutes()\" ng-class=\"{disabled: noDecrementMinutes()}\" class=\"btn btn-link\" ng-disabled=\"noDecrementMinutes()\" tabindex=\"{{::tabindex}}\"><span class=\"glyphicon glyphicon-chevron-down\"></span></a></td>\n" +
				"      <td ng-show=\"showMeridian\"><a ng-click=\"toggleMeridian()\" ng-class=\"{disabled: noToggleMeridian()}\" class=\"btn btn-link\" ng-disabled=\"noToggleMeridian()\" tabindex=\"{{::tabindex}}\"><span class=\"glyphicon glyphicon-chevron-down\"></span></a></td>\n" +
				"    </tr>\n" +
				"  </tbody>\n" +
				"</table>\n" +
				"");
		}]);
})(opakeApp, angular);


(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('opkTimepicker', ['$filter', '$compile', '$timeout', function ($filter, $compile, $timeout) {
			return {
				restrict: "E",
				scope: true,
				replace: true,
				bindToController: {
					ngModel: "=",
					ngDisabled:'='
				},
				controller: function ($scope) {
					var vm = this;
					vm.temp = '';

					vm.options = getOptionsFromHours([
						[[0, 0], [23, 45]]
					]);

					vm.setTime = function(val) {
						var date = moment(val, 'hh:mm a');
						if (date.isValid() ) {
							if (!angular.isDate(vm.ngModel)) {
								vm.ngModel = new Date();
							}
							date = date.toDate();
							vm.ngModel.setHours(date.getHours());
							vm.ngModel.setMinutes(date.getMinutes());
							vm.ngModel.setSeconds(date.getSeconds());
						}
						vm.reset();
					};

					vm.remove = function () {
						vm.ngModel = null;
					};

					vm.reset = function () {
						vm.temp = vm.ngModel ? $filter('date')(vm.ngModel, 'hh:mm a') : '';
					};

					$scope.$watch('timepickerVm.ngModel', function (newVal) {
						vm.reset();
					});

					function getOptionsFromHours(hours) {

						var result = [];

						angular.forEach(hours, function(h) {
							var start = h[0];
							var end = h[1];

							var minTime = moment({hour: start[0], minute: start[1]});
							var maxTime = moment({hour: end[0], minute: end[1]});

							var i = angular.copy(minTime);

							while (i <= maxTime) {
								result.push($filter('date')(i.toDate(), 'hh:mm a'));
								i.add(15, 'minutes');
							}
						});

						return result;
					}

				},
				controllerAs: 'timepickerVm',
				template: '<div class="opk-timepicker"></div>',
				link: function (scope, elem, attrs, ctrl) {
					var options = {
						editItem: true,
						listFilter: 'none',
						newItem: 'prompt',
						scrollToActiveElement: true,
						dropdownFilter: 'opkTimepickerSelectHighlight'
					};
					if (angular.isDefined(attrs.removable)) {
						options.removeItemFn = 'timepickerVm.remove()';
					}
					if (angular.isDefined(attrs.emptyRow)) {
						options.listFilter = 'opkSelectEmptyFieldTime';
					}
					var select = angular.element('<opk-select class="small" ng-disabled="timepickerVm.ngDisabled" ng-model="timepickerVm.temp" options="item for item in timepickerVm.options" placeholder="Select" select-options=\'' + JSON.stringify(options) + '\'></opk-select>');

					scope.$watch('timepickerVm.temp', function(newVal){
						if (newVal) {
							ctrl.setTime(newVal);
						}
					});

					elem.html(select);
					$compile(elem)(scope);
				}
			};
		}]);

		opakeApp.filter('opkTimepickerSelectHighlight', ['$sce', 'oiSelectEscape', function($sce, oiSelectEscape) {

			return function(label, query) {

				return $sce.trustAsHtml((function() {
					// 7 minutes
					var DIFF_TIME = 420;

					if (query.length > 0 || angular.isNumber(query)) {
						label = label.toString();
						query = oiSelectEscape(query.toString());

						var queryDate = moment(query, 'hh:mm a');
						if (queryDate.isValid()) {

							var diff = queryDate.diff(moment(label, 'hh:mm a'), 'seconds');
							if (!isNaN(diff)) {
								if (Math.abs(diff) <= DIFF_TIME) {
									return label.replace(label, '<strong class="current-active-label">$&</strong>');
								}
							}
						}
					}

					return label;
				}()));
			};
		}])
})(opakeApp, angular);
