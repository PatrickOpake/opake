(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('ReminderWidgetService', [
		'$q',
		'$http',
		function ($q, $http) {

			var widgetVm = null;
			var countOfReminders;

			this.init = function () {
				$http.get('/reminder/ajax/getCountReminders').then(function (result) {
					countOfReminders = result.data.count;
				});
			};

			this.assign = function(vm) {
				widgetVm = vm;
			};

			this.toggleShowWidget = function () {
				if (widgetVm) {
					widgetVm.toggleShowWidget();
				}
			};

			this.getUnreadSum = function () {
				return countOfReminders;
			};

		}]);
})(opakeApp, angular);
