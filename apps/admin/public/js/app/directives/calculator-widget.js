(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('calculatorWidget', ['View', function (View) {
			return {
				restrict: "E",
				replace: true,
				templateUrl: function () {
					return View.get('widgets/calculator.html');
				},
				link: function (scope, elem) {
					elem.find(".calculator").calculator(
						{layout:
							['M+CECA',
							'M-+-_/_*_-',
							'MR_7_8_9_+',
							'_%_4_5_6',
							'SR_1_2_3_=',
							'PI_0_.']
						}
					);
				}
			};
		}]);

})(opakeApp, angular);
