(function (opakeCore, angular) {
	'use strict';

	opakeCore.directive('ssn', [function () {
		return {
			restrict: "EA",
			replace: true,
			scope: {
				model: "=ngModel",
				ngDisabled: "="
			},
			template: '<div class="form-inline ssn">' +
					'<input type="text" ng-disabled="ngDisabled" ng-model="ssnObject.first" placeholder="###" ng-maxlength="3" limit-to-max-len valid-number move-next-on-maxlength class="form-control input-sm" /> <span> - </span> ' +
					'<input type="text" ng-disabled="ngDisabled" ng-model="ssnObject.second" placeholder="##" ng-disabled="ssnObject.first.length !== 3" ng-maxlength="2" limit-to-max-len valid-number move-next-on-maxlength class="form-control input-sm small-ssn-field" /> <span> - </span>' +
					'<input type="text" ng-disabled="ngDisabled" ng-model="ssnObject.third" placeholder="####" ng-disabled="ssnObject.second.length !== 2" ng-maxlength="4" valid-number limit-to-max-len class="form-control input-sm" />' +
				'</div>',
			link: function (scope, element, attrs) {
				var buildSSN = function(ssnobj) {
					scope.model = ssnobj.first + ssnobj.second + ssnobj.third;
					if(!scope.model) {
						scope.model = null;
					}
				};

				var divideSSN = function(ssn) {
					scope.ssnObject.first = ssn.slice(0, 3);
					scope.ssnObject.second = ssn.slice(3, 5);
					scope.ssnObject.third = ssn.slice(5, 9);
				};

				scope.ssnObject = {
					first: '',
					second: '',
					third: ''
				};

				if (scope.model) {
					divideSSN(scope.model);
				}

				scope.$watch("model",function() {
					var spliceOfSSNObject = scope.ssnObject.first + scope.ssnObject.second + scope.ssnObject.third;
					if (scope.model && scope.model !== spliceOfSSNObject) {
						divideSSN(scope.model);
					}

					if (!scope.model) {
						scope.ssnObject = {
							first: '',
							second: '',
							third: ''
						};
					}
				});

				scope.$watch('ssnObject', function () {
					buildSSN(scope.ssnObject);
				}, true);
			}
		};
	}]);

})(opakeCore, angular);
