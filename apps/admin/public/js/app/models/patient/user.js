(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('PatientUser', function () {

		var PatientUser = function (data) {
			angular.extend(this, data);

			if (this.patient && this.patient.dob) {
				this.patient.dob = moment(this.patient.dob).toDate();
			}

			if (this.created) {
				this.created = moment(this.created).toDate();
			}
			if (this.first_login_date) {
				this.first_login_date = moment(this.first_login_date).toDate();
			}
			if (this.last_login_date) {
				this.last_login_date = moment(this.last_login_date).toDate();
			}

		};

		return (PatientUser);
	});

})(opakeApp, angular);