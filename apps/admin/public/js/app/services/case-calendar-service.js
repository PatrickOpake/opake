// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('CaseCalendarService', [
		'$q',
		'uiCalendarConfig',

		function ($q, uiCalendarConfig) {

			this.get = function () {
				var def = $q.defer();
				var caseCalendar = uiCalendarConfig.calendars['case-calendar'];

				if (caseCalendar) {
					def.resolve(caseCalendar);
				}

				return def.promise;
			};


		}]);
})(opakeApp, angular);
