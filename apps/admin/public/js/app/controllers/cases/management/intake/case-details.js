(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseManagementIntakeCaseDetailsCtrl', [
		'$scope',
		'$window',
		'$location',
		function ($scope, $window, $location) {

			var vm = this;
			vm.isFormContentLoaded = false;

			$scope.$on('$includeContentLoaded', function() {
				vm.isFormContentLoaded = true;
			});

			var params = $location.search();
			if (angular.isDefined(params.fromCardsQueue) && (params.fromCardsQueue == 1)) {
				vm.fromCardsQueue = true;
			}

			vm.init = function (caseVm) {
				caseVm.edit();
			};

			vm.toCardsQueue = function () {
				$window.location = '/cases/' + $scope.org_id + '/cards/';
			};

		}]);

})(opakeApp, angular);
