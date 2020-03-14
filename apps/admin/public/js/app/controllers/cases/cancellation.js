// Case cancellation
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseCancellationCtrl', [
		'$scope',
		'$controller',
		'$uibModalInstance',
		'$http',
		'View',
		'Cases',
		'caseCancellation',
		'updateCancellation',
		'BeforeUnload',
		function ($scope, $controller, $uibModalInstance, $http, View, Cases, caseCancellation, updateCancellation, BeforeUnload) {

			$controller('ModalCrtl', {$scope: $scope, $uibModalInstance: $uibModalInstance});

			var vm = this;
			vm.caseCancellation = caseCancellation;
			vm.updateCancellation = updateCancellation;

			vm.cancel = function () {
				Cases.cancel(caseCancellation).then(function () {
					BeforeUnload.reset(true);
					$uibModalInstance.close();
				});
			};

			vm.save = function () {
				$http.post('/cases/ajax/save/' + $scope.org_id + '/cancellation/', $.param({
					data: JSON.stringify(caseCancellation)
				})).then(function () {
					$uibModalInstance.close();
				});
			};

		}]);

})(opakeApp, angular);
