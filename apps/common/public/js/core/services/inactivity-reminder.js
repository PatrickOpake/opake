(function (opakeCore, angular) {
	'use strict';

	opakeCore.service('InactivityReminder', [
		'$rootScope',
		'$document',
		'$http',
		'$interval',
		function ($rootScope, $document, $http, $interval) {

			var DEFAULT_INACTIVITY_TIME_LOGOUT = 1800;
			var IDLE_INTERVAL_CHECKING = 60;
			var COUNTDOWN_TIME = 60;

			var idleTime = 0;
			var idleInterval;
			var initialized = false;
			var isGoingToLogout = false;
			var siteLatestActivityTime;

			this.init = function(opts) {

				var logoutTime = opts.logoutTime ? opts.logoutTime : DEFAULT_INACTIVITY_TIME_LOGOUT;

				if (!initialized) {
					var self = this;
					idleInterval = $interval(function() {

						var nowTimestamp = Math.floor(new Date().getTime() / 1000);
						if (idleTime == 0) {
							if (angular.isFunction(opts.keepActive)) {
								opts.keepActive();
							}
							siteLatestActivityTime = nowTimestamp;
							storeSiteLatestActivityTime();
						}

						if (angular.isFunction(opts.checkLoggedIn)) {
							opts.checkLoggedIn();
						}

						idleTime = idleTime + 1;
						if (idleTime > ((logoutTime / COUNTDOWN_TIME) - 1)) {
							restoreSiteLatestActivityTime();
							if ((nowTimestamp - siteLatestActivityTime) >= (logoutTime - COUNTDOWN_TIME)) {
								siteLatestActivityTime = nowTimestamp;
								storeSiteLatestActivityTime();
								idleTime = 0;
								if (!isGoingToLogout) {
									showDialog();
								}
							}
						}
					}, IDLE_INTERVAL_CHECKING * 1000);

					$document.on('click', function (e) {
						idleTime = 0;
					});
					$document.on('keypress', function (e) {
						idleTime = 0;
					});

					initialized = true;
				}

				function storeSiteLatestActivityTime() {
					if (window.localStorage) {
						window.localStorage.setItem('siteLatestActivityTime', siteLatestActivityTime);
					}
				}

				function restoreSiteLatestActivityTime() {
					if (window.localStorage) {
						siteLatestActivityTime = parseInt(window.localStorage.getItem('siteLatestActivityTime') || siteLatestActivityTime);
					}
				}

				function showDialog() {

					$rootScope.dialog('/common/js/core/views/inactivity-reminder-modal.html', $rootScope, {
						backdrop: 'static',
						keyboard: false,
						controller:  [
							'$scope',
							'$uibModalInstance',
							function ($scope, $uibModalInstance) {

								isGoingToLogout = true;

								var vm = this;
								vm.idleCountdown = COUNTDOWN_TIME;
								var interval = $interval(function() {
									vm.idleCountdown--;
									if (vm.idleCountdown == 0) {
										$interval.cancel(interval);
										if (angular.isFunction(opts.logout)) {
											opts.logout();
										}
										$uibModalInstance.close('logout');
									}
								}, 1000);

								vm.stayOnline = function() {
									$interval.cancel(interval);
									if (angular.isFunction(opts.stayOnline)) {
										opts.stayOnline();
									}
									isGoingToLogout = false;
									$uibModalInstance.close('stayOnline');
								};

							}
						],
						controllerAs: 'ctrl'
					})
				}

			};

		}]
	);

})(opakeCore, angular);
