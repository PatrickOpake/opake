(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('emailForm', ['View', function (View) {
			return {
				restrict: 'E',
				replace: true,
				transclude: true,
				scope: true,
				templateUrl: function () {
					return View.get('email.html');
				},
				bindToController: {
					email: '='
				},
				controller: function () {
					var vm = this;

					vm.setCC = function () {
						vm.email.cc = [];
					};
					vm.setBCC = function () {
						vm.email.bcc = [];
					};
				},
				controllerAs: 'ef'
			};
		}]);

})(opakeApp, angular);
