(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('warningUnsavedForm', [
		'$timeout',
		'BeforeUnload',
		'InsurancesWidgetService',
		function ($timeout, BeforeUnload, InsurancesWidgetService) {
			return {
				restrict: "A",
				scope: {
					warningUnsavedForm: "="
				},
				link: function (scope, element, attrs) {
					var warnMsg = attrs.warningMsg || 'The form has been changed';

					var checkRegistration = function(){
						var oldForm = angular.copy(scope.warningUnsavedForm);

						var callback = function () {

							if (!BeforeUnload.compareForms(scope.warningUnsavedForm, oldForm)) {
								return warnMsg;
							}

							if (InsurancesWidgetService.hasWidget()) {
								if (InsurancesWidgetService.isCurrentEditInsuranceChanged() || InsurancesWidgetService.isModelInsurancesChanged()) {
									return warnMsg;
								}
							}

						};

						BeforeUnload.add(callback);

						scope.$on('$destroy', function(e) {
							BeforeUnload.remove(callback);
						});

						scope.$on('modal.closing', function(e) {
							if (!angular.equals(scope.warningUnsavedForm, oldForm)) {
								e.preventDefault();
							}
						});
					};

					if (angular.isDefined(attrs.warningIniTimeout)) { // TODO: remove after prod release, applying timeout only for one form for now
						$timeout(checkRegistration);
					} else {
						checkRegistration();
					}

				}
			};
		}]);

})(opakeApp, angular);
