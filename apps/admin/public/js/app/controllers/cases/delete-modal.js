// Case cancellation
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseDeleteModalCtrl', [
		'$scope',
		'$uibModalInstance',
		'Cases',
		'Calendar',
		'caseItem',
		function ($scope, $uibModalInstance, Cases, Calendar, caseItem) {

			var vm = this;
			vm.case = caseItem;

			vm.confirm = function() {
				Cases.delete(vm.case.id, function() {
					Calendar.refetchEvents();
				});
				$uibModalInstance.close();
			};

			vm.cancel = function() {
				$uibModalInstance.dismiss('cancel');
			};

		}]);

})(opakeApp, angular);
