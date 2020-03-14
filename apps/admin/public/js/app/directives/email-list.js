(function (opakeApp, angular) {
	'use strict';

	var EMAIL_REX = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

	opakeApp.directive('emailList', [function () {
			return {
				restrict: 'E',
				require: '?ngModel',
				replace: true,
				template: '<input />',
				link: function ($scope, elem, attrs, model) {

					model.$parsers.push(function (value) {
						var parsed = [];

						model.$setValidity('emailList', true);

						if (!value) {
							return parsed;
						}
						angular.forEach(value.split(','), function (email) {
							email = email.trim();
							if (EMAIL_REX.test(email)) {
								parsed.push(email);
							} else if (email !== '') {
								model.$setValidity('emailList', false);
							}
						});

						return parsed;
					});
				}
			};
		}]);

})(opakeApp, angular);
