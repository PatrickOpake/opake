(function (opakeCore, angular) {
	'use strict';

	opakeCore.controller('CasesFormsInfluenzaCtrl', [
		'vm',
		'$http',
		'InfluenzaForm',
		'DateConst',
		function (vm, $http, InfluenzaForm, DateConst) {

			var CHARACTER_LIMIT = 250;

			vm.characterLimit = CHARACTER_LIMIT;
			vm.form = null;
			vm.errors = null;

			vm.months = angular.copy(DateConst.MONTHS);
			vm.months.unshift({
				id: null,
				label: ''
			});


			vm.init = function() {
				return $http.get(vm.loadFormSrc).then(function (res) {
					if (res.data.success) {
						vm.form = new InfluenzaForm(res.data.form);
					} else {
						vm.form =  new InfluenzaForm({});
					}
					vm.illnesses = res.data.illnesses_list;
				});
			};

		}]);

})(opakeCore, angular);
