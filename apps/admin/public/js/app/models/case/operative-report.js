(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('OperativeReport', [
		'$filter',
		function ($filter) {

			var OperativeReport = function (data) {
				angular.extend(this, data);

				if(angular.isDefined(this.case) && angular.isDefined(this.case.time_start)) {
					this.case.time_start = new Date(this.case.time_start);
				}

				if (data.time_start) {
					this.time_start = moment(data.time_start).toDate();
				}

				if (data.time_submitted) {
					this.time_submitted = moment(data.time_submitted).toDate();
				}

				if (data.time_signed) {
					this.time_signed = moment(data.time_signed).toDate();
				}

				angular.forEach(data.amendments, function (amendment) {
					amendment.time_signed = moment(amendment.time_signed).toDate();
				});

				this.isSelf = function(user) {
					if (angular.isDefined(this.case)) {
						return this.case.users[0].id == user.id
						|| this.surgeon_id == user.id;
					}
				}

			};

			return (OperativeReport);
		}
	]);

	opakeApp.factory('OperativeReportTemplate', [function () {

		var OperativeReportTemplate = function (data) {

			angular.extend(this, data);

		};

		return (OperativeReportTemplate);
	}]);

	opakeApp.factory('OperativeReportFutureTemplate', [function () {

		var OperativeReportFutureTemplate = function (data) {

			angular.extend(this, data);

			if(this.updated) {
				this.updated = moment(this.updated).toDate();
			}

		};

		return (OperativeReportFutureTemplate);
	}]);
})(opakeApp, angular);