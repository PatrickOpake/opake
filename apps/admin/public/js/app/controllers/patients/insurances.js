(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PatientInsurancesCtrl', [
		'$scope',
		'$http',
		'View',
		'PatientInsurance',
		'PatientConst',

		function ($scope, $http, View, PatientInsurance, PatientConst) {

			var vm = this;

			vm.getDataTemplateSrc = function(item, isEdit) {

				var templateName = (isEdit) ? 'edit.html' : 'view.html';

				if (item.type == PatientConst.INSURANCE_TYPES_ID.AUTO_ACCIDENT) {
					return View.get('patients/insurances/auto-accident/' + templateName);
				}

				if (item.type == PatientConst.INSURANCE_TYPES_ID.WORKERS_COMP) {
					return View.get('patients/insurances/workers-company/' + templateName)
				}

				if (item.type == PatientConst.INSURANCE_TYPES_ID.LOP || item.type == PatientConst.INSURANCE_TYPES_ID.SELF_PAY) {
					return View.get('patients/insurances/description/' + templateName)
				}

				return View.get('patients/insurances/regular/' + templateName);
			};

		}]);

})(opakeApp, angular);