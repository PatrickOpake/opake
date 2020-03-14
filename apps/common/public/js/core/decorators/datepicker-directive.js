(function (opakeCore, angular) {
	'use strict';
	opakeCore.config(function($provide) {
		$provide.decorator('uibDatepickerDirective', function ($delegate) {
			var directive = $delegate[0];
			var directiveCompile = directive.compile;

			directive.compile = function () {
				var link = directiveCompile.apply(this, arguments);

				return function (scope, element, attrs, ctrls) {
					link.apply(this, arguments);

					var datepickerCtrl = ctrls[0];
					var ngModelCtrl = ctrls[1];

					if (ngModelCtrl) {
						scope.$on('refreshDatepickers', function refreshView() {
							datepickerCtrl.refreshView();
						});
					}

					var oldMove = scope.move;
					scope.move = function (direction) {
						oldMove.apply(this, arguments);
						scope.$emit('datepicker.monthChanged', this.activeDt.date);
					};

					var oldSelect = scope.select;
					scope.select = function (date) {
						oldSelect.apply(this, arguments);
						scope.$emit('datepicker.monthChanged', date);
					};
				}
			};
			return $delegate;
		});
	});


})(opakeCore, angular);