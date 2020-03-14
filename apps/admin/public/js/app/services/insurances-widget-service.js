(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('InsurancesWidgetService', [
		'$q',
		function ($q) {

			var widgetVm = null;

			this.init = function(vm) {
				widgetVm = vm;
			};

			this.tryToSaveOpenedInsurance = function() {
				if  (widgetVm && widgetVm.currentEditInsurance) {
					return widgetVm.saveCurrentEditInsurance();
				}

				var def = $q.defer();
				def.resolve();
				return def.promise;
			};

			this.hasWidget = function() {
				return !!widgetVm;
			};

			this.isCurrentEditInsuranceChanged = function() {
				return widgetVm && widgetVm.isCurrentEditInsuranceChanged();
			};

			this.isModelInsurancesChanged = function() {
				return widgetVm && widgetVm.isModelInsurancesChanged();
			};

		}]);
})(opakeApp, angular);
