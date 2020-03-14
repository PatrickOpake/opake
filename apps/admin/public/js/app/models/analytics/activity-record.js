(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('ActivityRecord', function () {

		var ActivityRecord = function (data) {

			angular.extend(this, data);

		};

		return (ActivityRecord);
	});

})(opakeApp, angular);