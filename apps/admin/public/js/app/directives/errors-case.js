// Temp directive for errors
(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('errorsCase', [function () {
			return {
				restrict: "E",
				replace: true,
				scope: {
					errors: "=src"
				},
				template: '<div ng-show="new_errors.length" class="alert alert-danger">' +
						'<span ng-if="new_errors" class="errors-count">{{new_errors.length}} <i class="fa fa-exclamation-triangle"></i></span>' +
						'<ul class="list-unstyled errors-msgs">' +
							'<li ng-repeat="error in new_errors" ng-bind-html="error"></li>' +
						'</ul>' +
					'</div>',
				link: function (scope, elem, attrs) {
					scope.$watch("errors",function() {
						scope.new_errors = [];
						angular.forEach(scope.errors, function(error_field) {
							angular.forEach(error_field, function(errors) {
								if(angular.isArray(errors)) {
									scope.new_errors.push(errors);
								} else {
									angular.forEach(errors, function(error) {
										scope.new_errors.push(error);
									});
								}

							});
						});
					});
				}
			};
		}]);

})(opakeApp, angular);
