(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('errors', [function () {
			return {
				restrict: "E",
				replace: true,
				bindToController: {
					errors: "=src",
					type: "@?"
				},
				controller: ['$scope', function($scope) {

					var vm = this;

					vm.drawError = function(error) {
						if (angular.isArray(error)) {
							return error[0];
						}

						return error;
					};

					vm.isShowErrors = function() {
						if (angular.isArray(vm.errors)) {
							return vm.errors.length;
						}

						return !!vm.errors;
					};

				}],
				controllerAs: 'ctrlVm',
				template: '<div ng-if="ctrlVm.isShowErrors()" class="alert" ng-class="{\'alert-danger\': !ctrlVm.type, \'alert-warning\': ctrlVm.type === \'warning\'}">' +
						'<ul class="list-unstyled">' +
							'<li ng-repeat="error in ctrlVm.errors track by $index" ng-bind-html="ctrlVm.drawError(error)"></li>' +
						'</ul>' +
					'</div>'
			};
		}]);

})(opakeCore, angular);
