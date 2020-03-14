(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('PatientInsurancesCtrl', [
		'$scope',
		'$http',
		'View',
		'PatientConst',

		function ($scope, $http, View, PatientConst) {

			var vm = this;
			vm.isTemplatesLoaded = false;

			$scope.$on('$includeContentLoaded', function() {
				vm.isTemplatesLoaded = true;
			});

			vm.getDataTemplateSrc = function(item, isEdit) {

				var templateName = (isEdit) ? 'edit.html' : 'view.html';

				if (item.type == PatientConst.INSURANCE_TYPES_ID.AUTO_ACCIDENT) {
					return View.get('app/insurance/auto-accident/' + templateName);
				}

				if (item.type == PatientConst.INSURANCE_TYPES_ID.WORKERS_COMP) {
					return View.get('app/insurance/workers-company/' + templateName)
				}

				if (item.type == PatientConst.INSURANCE_TYPES_ID.LOP || item.type == PatientConst.INSURANCE_TYPES_ID.SELF_PAY) {
					return View.get('app/insurance/description/' + templateName)
				}

				return View.get('app/insurance/regular/' + templateName);
			};

			vm.fillInsurancePayorData = function(insuranceItem) {

				if (insuranceItem.data.insurance.id) {
					$http.get('/api/insurances/getPayorInfo?id=' + insuranceItem.data.insurance.id).then(function(res) {
						if (res.data.success) {
							var payorData = res.data.data;
							if (payorData.insurance_type) {
								insuranceItem.type = payorData.insurance_type;
							}
							insuranceItem.data.address_insurance = payorData.address;
							insuranceItem.data.provider_phone = payorData.phone;
							insuranceItem.data.insurance_state = payorData.state;
							insuranceItem.data.insurance_city = payorData.city;
							insuranceItem.data.insurance_zip_code = payorData.zip_code;
						}
					});
				} else {
					insuranceItem.data.address_insurance = '';
					insuranceItem.data.provider_phone = '';
					insuranceItem.data.insurance_state = null;
					insuranceItem.data.insurance_city = null;
					insuranceItem.data.insurance_zip_code = '';
				}

			};


		}]);

})(opakeApp, angular);
