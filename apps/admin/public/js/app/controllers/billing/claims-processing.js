// Billing list
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ClaimsProcessingCtrl', [
		'$rootScope',
		'$scope',
		'View',
		function ($rootScope, $scope, View) {

			$scope.view = View;

			$rootScope.subTopMenu = {
				'process': 'Process',
				'processed': 'Processed',
				'resubmitted': 'Resubmitted',
				'onHold': 'On Hold',
				'exception': 'Exception'
			};

			$rootScope.subTopMenuActive = 'process';

		}]);

})(opakeApp, angular);
