// List of cases
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ModalCaseBlockingItemCtrl', [
		'$scope',
		'$http',
		'$controller',
		'$uibModalInstance',
		'Calendar',
		'View',
		'CalendarConst',
		'blockingItem',
		function ($scope, $http, $controller, $uibModalInstance, Calendar, View, CalendarConst, blockingItem) {

			$controller('ModalCrtl', {$scope: $scope, $uibModalInstance: $uibModalInstance});

			$scope.calendarConst = CalendarConst;

			var vm = this;
			vm.blockingItem = blockingItem;

			vm.save = function () {
				$http.post('/cases/ajax/blocking/' + $scope.org_id + '/saveItem/', $.param({
					data: JSON.stringify(vm.blockingItem.toJSON())
				})).then(function (result) {
					if (result.data.id) {
						$uibModalInstance.close();
						Calendar.refetchEvents();
					} else if (result.data.errors) {
						vm.errors = result.data.errors.split(';');
					}
				});
			};

			vm.delete = function () {
				$scope.dialog(View.get('cases/calendar/blocking/confirm_delete_item.html'), $scope, {windowClass: 'alert confirm-delete'}).result.then(function () {
					$http.post('/cases/ajax/blocking/' + $scope.org_id + '/deleteItem/' + vm.blockingItem.id).then(function () {
						$uibModalInstance.close();
						Calendar.refetchEvents();
					});
				});
			};

			vm.changeUser = function () {
				if (angular.isDefined(vm.blockingItem.surgeon_or_practice)) {
					vm.blockingItem.color = vm.blockingItem.surgeon_or_practice.case_color;
				}
			};

		}]);

})(opakeApp, angular);
