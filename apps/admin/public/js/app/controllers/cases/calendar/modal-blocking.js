// List of cases
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ModalCaseBlockingCtrl', [
		'$scope',
		'$controller',
		'$http',
		'$uibModalInstance',
		'View',
		'Calendar',
		'CaseBlockingConst',
		'CalendarConst',
		'blocking',
		'blockingItem',
		function ($scope, $controller, $http, $uibModalInstance, View, Calendar, CaseBlockingConst, CalendarConst, blocking, blockingItem) {

			$controller('ModalCrtl', {$scope: $scope, $uibModalInstance: $uibModalInstance});

			$scope.caseBlockingConst = CaseBlockingConst;
			$scope.calendarConst = CalendarConst;

			var vm = this;
			vm.blocking = blocking;
			vm.blockingItem = blockingItem;

			vm.isCreation = function () {
				return !blocking.id;
			};

			vm.save = function () {
				if (vm.blocking.monthly_every == 'day') {
					vm.blocking.month_number = vm.blocking.month_number_1;
				} else if (vm.blocking.monthly_every == 'weekday') {
					vm.blocking.month_number = vm.blocking.month_number_2;
				}

				$http.post('/cases/ajax/blocking/' + $scope.org_id + '/save/', $.param({
					data: JSON.stringify(vm.blocking.toJSON())
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
				$scope.dialog(View.get('cases/calendar/blocking/confirm_delete.html'), $scope, {windowClass: 'alert confirm-delete'}).result.then(function () {
					$http.post('/cases/ajax/blocking/' + $scope.org_id + '/delete/' + vm.blocking.id).then(function () {
						$uibModalInstance.close();
						Calendar.refetchEvents();
					});
				});
			};

			vm.changeUser = function () {
				if (angular.isDefined(vm.blocking.surgeon_or_practice)) {
					vm.blocking.color = vm.blocking.surgeon_or_practice.case_color;
				}
			};

			vm.toggleWeekDays = function (day) {
				var idx = vm.blocking.recurrence_week_days.indexOf(day);
				if (idx > -1) {
					vm.blocking.recurrence_week_days.splice(idx, 1);
				} else {
					vm.blocking.recurrence_week_days.push(day);
				}
			};

			vm.isWeekDayChecked = function (day) {
				return vm.blocking.recurrence_week_days.indexOf(day) > -1;
			};

		}]);

})(opakeApp, angular);
