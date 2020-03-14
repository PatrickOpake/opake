(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('PreOperativeForm',  [function () {

		var PreOperativeForm = function (data) {

			var self = this;
			angular.extend(this, data);

			if (this.pain_management) {
				angular.forEach(this.pain_management, function(pmRecord) {
					if (pmRecord.date) {
						pmRecord.date = moment(pmRecord.date).toDate();
					}
				});
			}

			if (!this.communicable_diseases) {
				this.communicable_diseases = [{name: ''}];
			}

			if (!this.cultural_limitations) {
				this.cultural_limitations = [{name: ''}];
			}

			this.toJSON = function() {
				var copy = angular.copy(this);

				if (copy.pain_management) {
					angular.forEach(copy.pain_management, function(pmRecord) {
						if (pmRecord.date) {
							pmRecord.date = moment(pmRecord.date).format('YYYY-MM-DD');
						}
					});
				}

				return copy;
			};

		};

		return (PreOperativeForm);
	}]);
})(opakeApp, angular);