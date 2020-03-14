// Time Log
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseTimeLogCtrl', [
		'$scope',
		'$http',
		'TimeLogConst',
		'CaseTimeLog',
		function ($scope, $http, TimeLogConst, CaseTimeLog) {
			var vm = this,
				caseId;
			vm.timeLogConst = TimeLogConst;

			vm.init = function (case_id) {
				caseId = case_id;
				$http.get('/cases/ajax/' + $scope.org_id + '/timeLog/' + caseId).then(function (result) {
					var timeLog = {};
					angular.forEach(result.data, function (log) {
						timeLog[log.stage] = new CaseTimeLog(log);
					});
					angular.forEach(TimeLogConst.STAGES, function (item) {
						if (angular.isUndefined(timeLog[item.code])) {
							timeLog[item.code] = new CaseTimeLog({stage:item.code});
						}
					});
					vm.timeLog = timeLog;

					$scope.$watch('logVm.timeLog', function (newVal, oldVal) {
						if (!angular.equals(newVal, oldVal)) {
							vm.save();
						}
					}, true);
				});
			};

			vm.save = function () {
				if (caseId && vm.timeLog) {
					$http.post('/cases/ajax/save/' + $scope.org_id + '/timeLog/' + caseId, $.param({data: JSON.stringify(vm.timeLog)}));
				}
			};

			vm.isShowCaseTotal = function () {
				return vm.timeLog &&
					vm.timeLog['enter_or'].time && vm.timeLog['operation_room_exit'].time &&
					vm.timeLog['enter_or'].time < vm.timeLog['operation_room_exit'].time;
			};

		}]);

})(opakeApp, angular);
