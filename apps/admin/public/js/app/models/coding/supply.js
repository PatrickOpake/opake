(function (opakeApp, angular) {
	'use strict';

	opakeApp.factory('CodingSupply', ['CodingConst', function (CodingConst) {

			var CodingSupply = function (data) {
				this.code = data.hcpcs;
				this.type = 'hcpcs';

				angular.extend(this, data);

				this.getType = function() {
					return CodingConst.SUPPLY_TYPES[this.type_id];
				};

				this.getTypeConst = function() {
					return CodingConst.SUPPLY_TYPES;
				}

			};

			return (CodingSupply);
		}]);
})(opakeApp, angular);