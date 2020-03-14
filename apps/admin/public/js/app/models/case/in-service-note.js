(function (opakeApp, angular, $) {
	'use strict';

	opakeApp.factory('InServiceNote', ['$filter', function ($filter) {

			var InServiceNote = function (data) {

				angular.extend(this, data);

				if (angular.isDefined(data)) {
					this.time_add = moment(data.time_add).toDate();
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

			};

			return (InServiceNote);
		}]);
})(opakeApp, angular, $);