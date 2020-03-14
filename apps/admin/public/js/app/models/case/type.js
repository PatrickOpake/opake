(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('CaseType', [
		'CPT',
		function (CPT) {

			var CaseType = function (data) {
				angular.extend(this, data);
				var self = this;

				this.is_active = (this.active == 1);

				this.cpts = [];
				if (data) {
					self.name = data.name;
					if (data.length) {
						self.length = moment(data.length, 'HH:mm').toDate();
					}
					angular.forEach(data.cpts, function (cpt) {
						self.cpts.push(new CPT(cpt));
					});
				}
			};

			return (CaseType);
		}]);
})(opakeApp, angular);
