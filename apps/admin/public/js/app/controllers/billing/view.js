// Billing view
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('BillingViewCtrl', [
		'$rootScope',
		'$scope',
		'$http',
		'$window',
		function ($rootScope, $scope, $http, $window) {

			var vm = this;
			$rootScope.topMenuActive = 'billing';

			vm.toBillingsQueue = function () {
				$window.location = '/billings/' + $scope.org_id;
			};


		}]);

})(opakeApp, angular);
