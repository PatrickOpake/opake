(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('CodingProcedure', [ 'CodingConst', function (CodingConst) {

			var CodingProcedure = function (data) {
				this.code = data.cpt;
				this.type = 'cpt';
				this.type_id = '0';

				angular.extend(this, data);

				this.getType = function() {
					return '';
				};

				this.getTypeConst = function() {
					return CodingConst.PROCEDURE_TYPES;
				}

			};



			return (CodingProcedure);
		}]);
})(opakeApp, angular);