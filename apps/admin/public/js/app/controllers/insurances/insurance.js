// Insurance view/edit
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('InsuranceCrtl', [
		'$scope',
		'$http',
		'$window',
		'config',
		'View',
		'Insurance',
		'InsuranceConst',
		'Insurances',
		function ($scope, $http, $window, config, View, Insurance, InsuranceConst, Insurances) {
			$scope.insurance_const = InsuranceConst;

			var vm = this;
			vm.insurance = null;

			vm.init = function (id) {
				if (id) {
					$http.get('/insurances/ajax/insurance/' + id).then(function (result) {
						vm.insurance = new Insurance(result.data);
					});
				} else {
					vm.insurance = {};
				}
			};

			vm.save = function () {
				var data = JSON.stringify(vm.toedit || vm.insurance);
				Insurances.save(data, function(result) {
					if (result.data.id) {
						$window.location = '/settings/insurances/';
					} else if (result.data.errors) {
						vm.errors = result.data.errors.split(';');
					}
				});
			};

			vm.cancel = function() {
				$window.location = '/settings/insurances/';
			};

			vm.getView = function () {
				var view = 'insurances/form.html';
				return View.get(view);
			};
		}]);

})(opakeApp, angular);
