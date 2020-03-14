// Filters
(function (opakeCore, angular) {
	'use strict';

	opakeCore.filter('range', function () {
		return function (input, min, max, step) {
			step = step || 1;
			for (var i = min; i <= max; i += step) {
				input.push(i);
			}
			return input;
		};
	});

	opakeCore.filter('keylength', function () {
		return function (input) {
			if (!angular.isObject(input)) {
				throw Error("Usage of non-objects with keylength filter!!");
			}
			return Object.keys(input).length;
		};
	});

	opakeCore.filter('number', function () {
		return function (input) {
			return parseInt(input, 10);
		};
	});

	opakeCore.filter('age', function () {
		return function (date) {
			if (date instanceof Date) {
				var ageDifMs = Date.now() - date.getTime();
				var ageDate = new Date(ageDifMs);
				return Math.abs(ageDate.getUTCFullYear() - 1970);
			}
		};
	});

	opakeCore.filter('exclude', function () {
		return function (input, exclude, propInp, probEx) {
			probEx = probEx || propInp;

			if (!angular.isArray(input))
				return input;

			if (!angular.isArray(exclude))
				exclude = [];

			if (probEx) {
				exclude = exclude.map(function (item) {
					return item[probEx];
				});
			}

			return input.filter(function (item) {
				return exclude.indexOf(propInp ? item[propInp] : item) === -1;
			});
		};
	});

	opakeCore.filter('ssn', function () {
		return function (ssn) {
			if (!ssn) {
				return '';
			}
			var result = ssn.slice(0, 3) + '-' + ssn.slice(3, 5) + '-' + ssn.slice(5, 9);
			return result.trim();
		};
	});

	opakeCore.filter('phone', function () {
		return function (phone) {
			if (!phone) {
				return '';
			}
			var result = phone.slice(0, 3) + '-' + phone.slice(3, 6) + '-' + phone.slice(6, 10);
			return result.trim();
		};
	});

	opakeCore.filter('usd', function () {
		return function ($value) {
			return '$' + $value;
		};
	});

	opakeCore.filter('timeLength', function () {
		return function (start, end) {
			var minutes = moment(end).diff(moment(start), 'minutes', true);
			var hours = Math.floor(minutes / 60);
			var minutes = Math.round(minutes % 60);

			var result = ((hours) ? hours + ' hr ' : '') + ((minutes) ? minutes + ' min' : '');
			return result.trim();
		};
	});

})(opakeCore, angular);
