
var opakeCore = angular.module('opakeCore', ['ngSanitize', 'ui.bootstrap', 'oi.select', 'ng-sortable']);

opakeCore.run([
	'$http',
	'uibDatepickerPopupConfig',
	function ($http, uibDatepickerPopupConfig) {
		$http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";

		uibDatepickerPopupConfig.datepickerPopup = "M/d/yyyy";
		uibDatepickerPopupConfig.altInputFormats = ["M!/d!/yyyy"];
		uibDatepickerPopupConfig.showButtonBar = false;
		uibDatepickerPopupConfig.onOpenFocus = false;

	}]);
