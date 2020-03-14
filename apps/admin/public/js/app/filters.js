(function(opakeApp, angular) {
	'use strict';

	opakeApp.filter('money', ['$filter', function() {
		return function(number, options) {
			options = options || {};
			if (angular.isUndefined(number) || number === null || number === '') {
				number = 0.0;
			}
			if (!angular.isNumber(number)) {
				number = parseFloat(number);
			}
			if (!isFinite(number) || isNaN(number)) {
				number = 0.0;
			}

			if (!options.reduceZeros) {
				number = number.toFixed(2);
			} else {
				if (number % 1 != 0) {
					number = number.toFixed(2);
				} else {
					number = number.toFixed(0);
				}
			}

			if (number === '-0') {
				number = '0';
			} else if (number === '-0.00') {
				number = '0.00';
			}

			return '$' + number;
		}
	}]);

})(opakeApp, angular);