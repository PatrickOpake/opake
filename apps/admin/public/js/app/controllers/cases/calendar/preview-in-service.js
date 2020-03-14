// Create case
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PreviewCaseInServiceCrtl', [
		'$scope',
		'$window',
		'$http',
		'Permissions',
		'View',
		'Cases',
		'Calendar',
		function ($scope, $window, $http, Permissions, View, Cases, Calendar) {


			var vm = this;

			vm.init = function (item) {
				vm.item = item;
			};

			vm.edit = function () {
				Calendar.editInService(vm.item);
			};

			vm.delete = function () {
				$scope.dialog(View.get('patients/confirm_delete_zero.html'), $scope, {windowClass: 'alert'}).result.then(function () {
					$http.post('/cases/ajax/' + $scope.org_id + '/deleteInService/' + vm.item.id).then(function () {
						Calendar.refetchEvents();
					});
				});
			};


		}]);
})(opakeApp, angular);
