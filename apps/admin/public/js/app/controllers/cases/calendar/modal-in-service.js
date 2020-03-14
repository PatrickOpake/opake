// Create case
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('ModalInServiceCrtl', [
		'$scope',
		'$controller',
		'$http',
		'$location',
		'$uibModalInstance',
		'View',
		'Cases',
		'Calendar',
		'Patient',
		'uiCalendarConfig',
		'CaseRegistrationConst',
		'item',
		'CaseCancellation',
		'CaseCalendarService',
		function ($scope, $controller, $http, $location, $uibModalInstance, View, Cases, Calendar, Patient, uiCalendarConfig, CaseRegistrationConst, item, CaseCancellation, CaseCalendarService) {

			$controller('ModalCrtl', {$scope: $scope, $uibModalInstance: $uibModalInstance});

			$scope.caseRegistrationConst = CaseRegistrationConst;

			var vm = this;
			vm.item = item;

			vm.save = function () {
				checkPassed(function () {
					$http.post('/cases/ajax/save/' + $scope.org_id + '/saveInService/', $.param({data: JSON.stringify(vm.item)})).then(function (result) {
						if (result.data.id) {
							$uibModalInstance.close(result.data.id);
							Calendar.refetchEvents();
							$location.search('');
						} else if (result.data.errors) {
							vm.errors = result.data.errors.split(';');
						}
					});
				});
			};

			vm.caseDateOfServiceChanged = function(newDate) {
				CaseCalendarService.get().then(function(caseCalendar) {
					caseCalendar.fullCalendar('changeView', 'agendaDay');
					caseCalendar.fullCalendar('gotoDate', newDate);
				});
			};

			function checkPassed(confirm) {
				if (vm.item.start <= new Date()) {
					$scope.dialog(View.get('cases/passed_confirm.html'), $scope, {windowClass: 'alert'}).result.then(function () {
						confirm();
					});
				} else {
					confirm();
				}
			}

		}]);
})(opakeApp, angular);
