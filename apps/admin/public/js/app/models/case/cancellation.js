(function (opakeApp, angular, $) {
	'use strict';

	opakeApp.factory('CaseCancellation', ['CaseCancelAttempt', function (CaseCancelAttempt) {

			var CaseCancellation = function (data) {

				var self = this;

				angular.extend(this, data);

				if (angular.isDefined(data)) {
					if (data.dos) {
						this.dos = moment(data.dos).toDate();
					}
					if (data.cancel_time) {
						this.cancel_time = moment(data.cancel_time).toDate();
					}
					if (data.rescheduled_date) {
						this.rescheduled_date = moment(data.rescheduled_date).toDate();
					}
					if (data.case_time_start) {
						this.case_time_start = moment(data.case_time_start).toDate();
					}
					if (data.case_time_end) {
						this.case_time_end = moment(data.case_time_end).toDate();
					}
				}

				this.cancel_attempts = [];
				if (angular.isDefined(data) && data.cancel_attempts && data.cancel_attempts.length) {
					angular.forEach(data.cancel_attempts, function (attempt) {
						self.cancel_attempts.push(new CaseCancelAttempt(attempt));
					});
				} else {
					for (var i = 1; i <= 3; i++) {
						var attempt = new CaseCancelAttempt();
						attempt.case_cancellation_id = this.id;
						this.cancel_attempts.push(attempt);
					}
				}

				this.getCancelReason = function() {
					if (this.cancel_reason) {
						if (this.cancel_reason.length > 100) {
							return '-' + this.cancel_reason.substring(0, 100) + ' ...';
						} else {
							return '-' + this.cancel_reason;
						}
					} else {
						return '';
					}
				};

				this.toJSON = function() {
					var copy = angular.copy(this);
					if (copy.dos) {
						copy.dos = moment(copy.dos).format('YYYY-MM-DD HH:mm:ss');
					}
					if (copy.case_time_start) {
						copy.case_time_start = moment(copy.case_time_start).format('YYYY-MM-DD HH:mm:ss');
					}
					if (copy.case_time_end) {
						copy.case_time_end = moment(copy.case_time_end).format('YYYY-MM-DD HH:mm:ss');
					}
					if (copy.cancel_time) {
						copy.cancel_time = moment(copy.cancel_time).format('YYYY-MM-DD HH:mm:ss');
					}

					return copy;
				};

			};

			return (CaseCancellation);
		}]);
})(opakeApp, angular, $);