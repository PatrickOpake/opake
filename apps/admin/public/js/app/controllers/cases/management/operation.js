// H&P docs
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseOperationCrtl', [
		'$scope',
		'$http',

		function ($scope, $http) {

			var vm = this;

			vm.init = function (caseObj) {
				vm.case = caseObj;
			};

			vm.start = function () {
				$http.post('/cases/ajax/' + $scope.org_id + '/startCase/' + vm.case.id).then(function (resp) {
					vm.case.time_start_in_fact = new Date();
				});
			};
			vm.end = function () {
				$http.post('/cases/ajax/' + $scope.org_id + '/endCase/' + vm.case.id).then(function (resp) {
					vm.case.time_end_in_fact = new Date();
				});
				$scope.dialog('case/cm/clinical/operation_confirm.html', $scope, {windowClass: 'alert', backdrop: 'static'}).result.then(function () {
					
				});
			};

		}]);

})(opakeApp, angular);
