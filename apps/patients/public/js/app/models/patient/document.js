(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('CaseDocument', [
		function () {
			var CaseDocument = function (data) {
				angular.extend(this, data);

				if (this.dos) {
					this.dos = moment(data.dos).toDate();
				}

				if (this.uploaded_date) {
					this.uploaded_date = moment(data.uploaded_date).toDate();
				}

				this.getSurgeonNames = function() {
					return this.surgeons.map(function(v) {
						return v.fullname;
					}).join(', ');
				};

			};

			return (CaseDocument);
		}]);
})(opakeApp, angular);