(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('dateField', ['$compile', '$timeout', function ($compile, $timeout) {
			return {
				restrict: "E",
				require: 'ngModel',
				replace: true,
				transclude: true,
				scope: {
					ngModel: "=",
					ngDisabled:'=',
					ngChange: '&',
					dateOnly: '=?'
				},
				template: '<div class="date" ng-class="{\'has-left-right-buttons\': showLeftRightButtons}">' +
							'<button class="btn btn-link prev-day-button" ng-show="showLeftRightButtons" ng-click="prevDay()"><i class="glyphicon glyphicon-chevron-left"></i></button>' +
							'<div class="datepicker-field">' +
								'<input type="text" class="form-control" placeholder="mm/dd/yyyy" ng-model="ngModel"\n\
									uib-datepicker-popup datepicker-append-to-body="true" is-open="open" ng-disabled="ngDisabled"\n\
									datepicker-options="options"\n\
									ng-change="change(ngModel)" />' +
							'</div>' +
							'<button class="btn btn-link next-day-button" ng-show="showLeftRightButtons" ng-click="nextDay()"><i class="glyphicon glyphicon-chevron-right"></i></button>' +
						'</div>',

				link: function (scope, elem, attrs) {
					var input = elem.find('input');

					scope.open = false;
					scope.options = {
						showWeeks: false
					};

					if (scope.dateOnly) {
						scope.options.timezone = 'utc';
					}

					scope.change = function (m) {
						$timeout(function(){
							if (scope.ngModel instanceof Date) {
								input.change();
							}
							scope.ngChange();
						});
					};

					input.change(function () {
						if (!scope.ngModel) {
							input.val('');
						}
					});

					if (attrs.name) {
						input.attr("name", attrs.name);
					}
					if (angular.isDefined(attrs.placeholder)) {
						input.attr("placeholder", attrs.placeholder);
					}
					if (attrs.size) {
						input.addClass("input-" + attrs.size);
					}
					if (attrs.icon) {
						elem.find('.datepicker-field').append('<i class="icon-calendar-input"></i>');
					}
					if (attrs.format) {
						input.attr('uib-datepicker-popup', attrs.format);
						$compile(input)(scope);
					}
					if (attrs.small) {
						input.addClass("input-sm");
					}

					scope.showLeftRightButtons = attrs.leftRightButtons;

					scope.nextDay = function() {
						if (scope.ngModel) {
							var date = moment(scope.ngModel);
							date.add(1, 'days');
							scope.ngModel = date.toDate();
							scope.change();
						}
					};

					scope.prevDay = function() {
						if (scope.ngModel) {
							var date = moment(scope.ngModel);
							date.subtract(1, 'days');
							scope.ngModel = date.toDate();
							scope.change();
						}
					};

					if (!attrs.withoutCalendar) {
						input.click(function (e) {
							scope.$apply(function () {
								scope.open = !scope.open;
							});
							e.preventDefault();
						});
					}
				}
			};
		}]);

})(opakeCore, angular);
