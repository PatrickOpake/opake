(function (opakeApp, angular) {
	'use strict';
	opakeApp.directive('validNumber', function () {
		return {
			require: '?ngModel',
			link: function (scope, elem, attrs, ngModelCtrl) {
				if (!ngModelCtrl) {
					return;
				}

				if (attrs.limit) {
					var limit = parseFloat(attrs.limit);
				}

				if (attrs.digitsMaxLength) {
					var digitsMaxLength = parseInt(attrs.digitsMaxLength);
				}

				ngModelCtrl.$parsers.push(function (val, v2) {
					var clean;
					if (angular.isUndefined(val) || val === null) {
						var val = '';
					}

					if (attrs.typeNumber === 'float') {
						clean = val.replace(/[^0-9.]+/g, '').match(/\d*(\.\d{0,2})?/);
						clean = clean ? clean[0] : '';
					} else {
						clean = val.replace(/[^0-9]+/g, '');
					}

					if (angular.isDefined(limit) && parseFloat(clean) > limit) {
						clean = attrs.limit;
					}

					if (angular.isDefined(digitsMaxLength)) {
						if (attrs.typeNumber === 'float') {
							var integerPartLength = digitsMaxLength - 2;
							var cleanArray = clean.split('.');
							if (cleanArray[0].length > integerPartLength) {
								cleanArray[0] = cleanArray[0].substr(0, integerPartLength);
								clean = cleanArray.join('.');
							}
						} else {
							if (clean.length > digitsMaxLength) {
								clean = clean.substr(0, digitsMaxLength);
							}
						}
					}

					if (val !== clean) {
						ngModelCtrl.$setViewValue(clean);
						ngModelCtrl.$render();
					}
					return clean;
				});

				if (attrs.typeNumber === 'float') {
					elem.blur(function () {
						if (elem.val()) {
							elem.val(parseFloat(elem.val()).toFixed(2));
						}
					});
				}

			}
		};
	});
})(opakeApp, angular);
