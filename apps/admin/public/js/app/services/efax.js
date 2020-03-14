(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('EfaxWidgetService', [
		'$q',
		function ($q) {

			var widgetVm = null;

			this.assign = function(vm) {
				widgetVm = vm;
			};

			this.toggleShowWidget = function () {
				if (widgetVm) {
					widgetVm.toggleShowWidget();
				}
			};

		}]);
})(opakeApp, angular);
