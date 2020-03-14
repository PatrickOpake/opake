// List of cases
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PreviewCaseBlockingCtrl', [
		'$scope',
		'View',
		'Permissions',
		'Calendar',
		'CaseBlockingConst',
		'CalendarConst',
		'Case',
		'CaseBlocking',
		function ($scope, View, Permissions, Calendar, CaseBlockingConst, CalendarConst, Case, CaseBlocking) {

			$scope.caseBlockingConst = CaseBlockingConst;
			$scope.calendarConst = CalendarConst;

			var vm = this;

			vm.hasBlockEditPermission = Permissions.hasAccess('case_blocks', 'edit');
			vm.hasCaseCreatePermission = Permissions.hasAccess('cases', 'create');

			vm.init = function (blocking, blockingItem) {
				vm.blocking = blocking;
				vm.blockingItem = blockingItem;
			};

			vm.edit = function () {
				var editTypeSelected = false;
				var modal = $scope.dialog(View.get('cases/calendar/blocking/edit_choice.html'), $scope, {windowClass: 'alert'});
				modal.result.then(function () {
					editTypeSelected = true;
				});
				modal.closed.then(function () {
					if (editTypeSelected) {
						if (vm.edit_type == 'item') {
							var blockingItem = new CaseBlocking(vm.blockingItem);
							blockingItem.start = moment(vm.blockingItem.start).toDate();
							blockingItem.end = moment(vm.blockingItem.end).toDate();
							Calendar.editBlockItem(blockingItem);
						}
						if (vm.edit_type == 'series') {
							Calendar.editBlock(vm.blocking);
						}
					}
				});
			};

			vm.scheduleCase = function () {
				var newCase = new Case();
				newCase.time_start = moment(vm.blockingItem.start).toDate();
				newCase.time_end = moment(vm.blockingItem.end).toDate();
				newCase.users.push(vm.blockingItem.doctor);
				newCase.location = vm.blockingItem.location;
				newCase.fromBlocking = true;
				Calendar.createCase(newCase);
			};

		}]);

})(opakeApp, angular);
