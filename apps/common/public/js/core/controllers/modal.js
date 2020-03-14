// Abstract Modal Controller
(function (opakeCore, angular) {
	'use strict';

	opakeCore.controller('ModalCrtl', [
		'$scope',
		'$uibModalInstance',
		function ($scope, $uibModalInstance) {

			$scope.ok = function (result) {
				$uibModalInstance.close(result);
			};

			$scope.cancel = function () {
				$uibModalInstance.dismiss('cancel');
			};
		}]);

})(opakeCore, angular);
