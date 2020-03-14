(function (opakeApp, angular, $) {
	'use strict';

	opakeApp.factory('CaseBlocking', [function () {

			var CaseBlocking = function (data) {
				var self = this;

				angular.extend(this, data);

				if (!angular.isDefined(data)) {
					this.recurrence_week_days = [];
					this.date_from = moment().add(1, 'days').toDate();
					this.date_to = moment().add(1, 'days').toDate();
					this.time_from = moment({hour: 12, minute: 0}).toDate();
					this.time_to = moment({hour: 13, minute: 0}).toDate();
					this.overwrite = false;
				} else {
					this.date_from = moment(data.date_from).toDate();
					this.date_to = moment(data.date_to).toDate();
					this.time_from = moment(data.time_from, 'HH:mm:ss').toDate();
					this.time_to = moment(data.time_to, 'HH:mm:ss').toDate();
				}

				this.toJSON = function() {
					var copy = angular.copy(self);
					if(self.date_from) {
						copy.date_from = moment(self.date_from).format('YYYY-MM-DD');
					}
					if(self.date_to) {
						copy.date_to = moment(self.date_to).format('YYYY-MM-DD');
					}
					if(self.time_from) {
						copy.time_from = moment(self.time_from).format('HH:mm:ss');
					}
					if(self.time_to) {
						copy.time_to = moment(self.time_to).format('HH:mm:ss');
					}

					return copy;
				};

			};

			return (CaseBlocking);
		}]);
})(opakeApp, angular, $);