(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('switch', ['$timeout', function ($timeout) {
		return {
			restrict: "AE",
			require: 'ngModel',
			replace: true,
			transclude: true,
			scope: {
				ngModel: '=',
				ngChange : '&',
				showLabels: '=',
				ngDisabled: '='
			},
			template:
				'<span class="switch" ng-click="change()" ng-class="{ checked: ngModel, disabled: ngDisabled }">' +
					'<span class="on" ng-show="showLabels">On</span>' +
				   '<small></small>' +
					'<span class="off" ng-show="showLabels">Off</span>' +
				   '<input type="checkbox"  ng-model="ngModel" style="display:none" />'+
				 '</span>'
			,
			link: function (scope, element, attrs, ngModel) {
				element.hide();
				scope.$watch("ngModel", function() {
					if (!angular.isUndefined(scope.ngModel)) {
						$timeout(function () {
							element.show();
						});
					}
				});

				scope.change = function() {
					if (!angular.isUndefined(scope.ngDisabled) && scope.ngDisabled) {
						return null;
					}

					if (isNaN(parseInt(scope.ngModel))) {
						scope.ngModel = !scope.ngModel;
					} else {
						scope.ngModel = +!scope.ngModel;
					}

					//TODO: Изменить вызов ng-change из этой директивы без передачи параметра
					if(scope.ngChange) {
						scope.ngChange({status: scope.ngModel});
					}
				};
			}
		};
	}]);

})(opakeCore, angular);
