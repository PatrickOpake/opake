// Temp directive for errors
(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('alertFlash', [
		'$rootScope',
		'$timeout',
		'$q',
		'$sce',
		function ($rootScope, $timeout, $q, $sce) {
			return {
				restrict: "EA",
				replace: true,
				scope: {
					message: "@"
				},
				template: '<div ng-click="closeAlert()" class="alert-flash" ng-class="{fixed: isFixed}" ng-show="isShowAlert"><div class="message-container">' +
				'<div class="message" ng-bind-html="htmlMessage"></div><a href="" class="close-button">×</a></div></div>',

				link: function (scope, elem, attrs) {

					var CLOSE_ALERT_TIME = 2000;
					var SCROLL_PIXELS_OFFSET = 120;
					var def = null;
					var doc = $(document);

					scope.isShowAlert = false;
					scope.closeAlert = function() {
						scope.isShowAlert = false;
					};

					$rootScope.$on('flashAlertMessage', function(e, message) {
						showAlert(message);
					});

					if (scope.message) {
						showAlert(scope.message);
					}

					doc.on('scroll', function() {
						if (doc.scrollTop() > SCROLL_PIXELS_OFFSET) {
							elem.addClass('fixed');
						} else {
							elem.removeClass('fixed');
						}

					});

					function showAlert(message) {
						if (message) {

							if (def) {
								def.reject();
							}

							scope.isShowAlert = true;
							scope.htmlMessage = $sce.trustAsHtml(message);

							def = $q.defer();
							def.promise.then(function() {
								scope.isShowAlert = false;
							});

							$timeout(function() {
								def.resolve();
							}, CLOSE_ALERT_TIME)

						}
					}

				}
			};
		}
	]);

})(opakeApp, angular);
