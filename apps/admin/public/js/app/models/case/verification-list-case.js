(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('VerificationListCase', [
		'$filter',

		function ($filter) {

			var VerificationListCase = function (data) {

				angular.extend(this, data);

				if (this.time_start) {
					this.time_start = moment(data.time_start).toDate();
				}
				if (this.time_end) {
					this.time_end = moment(data.time_end).toDate();
				}
				if (this.patient && this.patient.dob) {
					this.patient.dob = moment(data.patient.dob).toDate();
				}

				this.getDate = function(){
					if ((new Date()).toDateString() === this.time_start.toDateString()) {
						return $filter('date')(this.time_start, 'h:mm a');
					} else {
						return $filter('date')(this.time_start, 'M/d/yyyy');
					}
				};

				this.isVerificationBegin = function() {
					return this.verification_status == 0;
				};

				this.isVerificationContinue = function() {
					return this.verification_status == 1;
				};

				this.isVerificationCompleted = function() {
					return this.verification_status == 2;
				};

			};

			return (VerificationListCase);
		}]);
})(opakeApp, angular);