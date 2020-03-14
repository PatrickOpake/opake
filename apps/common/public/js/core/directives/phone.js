(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('phone', ['$rootScope', function ($rootScope) {
		return {
			restrict: "EA",
			replace: true,
			scope: {
				model: "=ngModel",
				ngDisabled: "=",
				phone: "=",
				size: '@'
			},
			template: '<div class="form-inline phone">' +
				'<input type="text" ng-disabled="ngDisabled" ng-model="phoneObject.first" placeholder="###" ng-maxlength="3" limit-to-max-len valid-number move-next-on-maxlength class="form-control w70" /> <span> - </span> ' +
				'<input type="text" ng-disabled="ngDisabled" ng-model="phoneObject.second" placeholder="###" ng-disabled="phoneObject.first.length !== 3" ng-maxlength="3" limit-to-max-len valid-number move-next-on-maxlength class="form-control w70" /> <span> - </span>' +
				'<input type="text" ng-disabled="ngDisabled" ng-model="phoneObject.third" placeholder="####" ng-disabled="phoneObject.second.length !== 3" ng-maxlength="4" valid-number limit-to-max-len class="form-control w70" />' +
				'</div>',
			link: function (scope, element, attrs) {

				scope.isSmallSize = true;
				scope.isStdSize = false;

				if (scope.size === 'std') {
					scope.isSmallSize = false;
					scope.isStdSize = true;
				}

				if (scope.isSmallSize) {
					element.find('input').addClass('input-sm');
				}

				var buildPhone = function(phoneobj) {
					scope.model = phoneobj.first + phoneobj.second + phoneobj.third;
					if (!scope.model) {
						scope.model = null;
					}
				};

				var dividePhone = function(phone) {
					scope.phoneObject.first = phone.slice(0, 3);
					scope.phoneObject.second = phone.slice(3, 6);
					scope.phoneObject.third = phone.slice(6, 10);
				};

				scope.phoneObject = {
					first: '',
					second: '',
					third: ''
				};

				scope.$watch("model",function(newValue, oldValue) {
					var spliceOfPhoneObject = scope.phoneObject.first + scope.phoneObject.second + scope.phoneObject.third;
					if (scope.model && scope.model !== spliceOfPhoneObject) {
						dividePhone(scope.model);
					}

					if (!scope.model) {
						scope.phoneObject = {
							first: '',
							second: '',
							third: ''
						};
					}
				});

				scope.$watch('phoneObject', function() {
					buildPhone(scope.phoneObject);
				}, true);
			}
		};
	}]);

})(opakeCore, angular);