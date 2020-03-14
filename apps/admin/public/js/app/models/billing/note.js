(function (opakeApp, angular, $) {
	'use strict';

	opakeApp.factory('BillingNote', ['$filter', '$rootScope', function ($filter, $rootScope) {

			var BillingNote = function (data) {

				angular.extend(this, data);

				if (angular.isDefined(data)) {
					this.time_add = moment(data.time_add).toDate();
					if(angular.isDefined(data.reminder) && data.reminder.id) {
						this.reminder.reminder_date = moment(data.reminder.reminder_date).toDate()
					}
				}

				this.getDate = function() {
					if ((new Date()).toDateString() === this.time_add.toDateString()) {
						return $filter('date')(this.time_add, 'h:mm a');
					} else {
						return $filter('date')(this.time_add, 'M/d/yyyy');
					}
				};

				this.getAnnotation = function() {
					if (this.text.length > 70) {
						return this.text.substring(0, 70) + ' ...';
					} else {
						return this.text;
					}
				};

				this.isSetReminder = function () {
					return angular.isDefined(this.reminder) && this.reminder.id;
				};

				this.showFlagAndReminderIcon = function () {
					return (this.user_id == $rootScope.loggedUser.id) && (this.calendar_is_open || this.isSetReminder());
				};

			};

			return (BillingNote);
		}]);
})(opakeApp, angular, $);