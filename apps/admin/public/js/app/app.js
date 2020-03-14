var opakeApp = angular.module('opake', ['opakeCore', 'ui.calendar', 'xeditable', 'dndLists', 'ngAnimate', 'ui.tinymce', 'ngFileUpload', 'moveable-modal']);

opakeApp.config(['moveableModalProvider', function (moveableModal) {
		moveableModal.options = {
			elSelector: '.modal-header',
			targetSelector: '.modal-content'
		};
	}]);

opakeApp.config(['$httpProvider', function($httpProvider) {
	$httpProvider.interceptors.push('NoCacheInterceptor');
	$httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
}]);

/*opakeApp.config(['$compileProvider', function ($compileProvider) {
	$compileProvider.debugInfoEnabled(false);
}]);*/

opakeApp.factory('NoCacheInterceptor', function () {

	function endsWith(str, suffix) {
		return (str.indexOf(suffix, str.length - suffix.length) !== -1);
	}

	return {
		request: function (config) {
			if (config.method == 'GET') {
				var parser = document.createElement('a');
				parser.href = config.url;
				var path = parser.pathname;

				if (!(endsWith(path, '.html') || endsWith(path, '.css') || endsWith(path, '.js'))) {
					config.headers['If-Modified-Since'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
					config.headers['Cache-Control'] = 'no-cache';
					config.headers['Pragma'] = 'no-cache';
				}
			}

			return config;
		}
	};
});

opakeApp.run([
	'$rootScope',
	'$http',
	'$uibModal',
	'editableOptions',
	'appInitData',
	'View',
	'Source',
	'User',
	'Permissions',
	'Messaging',
	'InactivityReminder',
	'MenuCounter',
	'EfaxWidgetService',
	'ReminderWidgetService',
	function ($rootScope, $http, $uibModal,
	          editableOptions, appInitData, View, Source, User, Permissions,
	          Messaging, InactivityReminder, MenuCounter, EfaxWidgetService,
		  ReminderWidgetService) {

		editableOptions.theme = 'bs3';

		$rootScope.app = {
			version: appInitData.version,
			year: appInitData.year
		};

		$rootScope.$log = function() {
			console.log.apply(console, arguments);
		};

		$rootScope.leftMenuConfig = appInitData.leftMenuConfig;

		View.setMarker(appInitData.debugmode ? new Date().getTime() : appInitData.versionTag);

		$rootScope.view = View;
		$rootScope.source = Source;
		$rootScope.permissions = Permissions;
		$rootScope.menuCounter = MenuCounter;

		if (appInitData.orgId) {
			$rootScope.org_id = appInitData.orgId;
		}
		if (appInitData.loggedUser) {
			$rootScope.loggedUser = new User(appInitData.loggedUser);
		}

		if (Permissions.hasAccess('chat', 'messaging')) {
			Messaging.init();
			$rootScope.messaging = Messaging;
		}

		$rootScope.efaxWidgetService = EfaxWidgetService;

		ReminderWidgetService.init();
		$rootScope.reminderWidgetService = ReminderWidgetService;

		$rootScope.dialog = function (tmpl, scope, opts) {
			var options = {
				scope: scope,
				controller: 'ModalCrtl',
				templateUrl: tmpl,
				size: 'sm'
			};

			if (opts) {
				options = angular.extend(options, opts);
			}

			return $uibModal.open(options);
		};

		//$httpProvider.defaults.headers.get['If-Modified-Since'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
		//$httpProvider.defaults.headers.get['Cache-Control'] = 'no-cache';
		//$httpProvider.defaults.headers.get['Pragma'] = 'no-cache';

		if (appInitData.loggedUser && !appInitData.debugmode && (!appInitData.inactivityReminder || appInitData.inactivityReminder.enabled)) {

			InactivityReminder.init({
				logoutTime: appInitData.inactivityReminder ? appInitData.inactivityReminder.logoutTime : null,
				logout: function() {
					window.onbeforeunload = null;
					window.location = '/auth/logout';
				},
				stayOnline: function() {
					$http.post('/clients/ajax/refreshExpires');
				},
				keepActive: function() {
					$http.post('/clients/ajax/keepActive');
				},
				checkLoggedIn: function() {
					$http.post('/clients/ajax/checkLoggedIn').then(function(res) {
						if (!res.data.logged) {
							window.onbeforeunload = null;
							location.reload();
						}
					});
				}
			});
		}

		if (appInitData.loggedUser && appInitData.loggedUser.is_temp_password) {
			showTempPasswordModal();
		}

		function showTempPasswordModal() {
			var $uibModalScope = $rootScope.$new(false, $rootScope);
			var modalOptions = {
				backdrop: 'static',
				keyboard: false,
				size: 'sm',
				controller: function ($scope, $uibModalInstance) {

					$scope.scheduledPasswordChange = appInitData.loggedUser.is_scheduled_password_change;
					$scope.countOfDaysForPasswordChange = appInitData.passwordChangeReminder.daysCount;

					$scope.savePassword = function () {

						$scope.currentError = '';
						if (!$scope.newPassword || !$scope.newPasswordConfirm) {
							$scope.currentError = 'Both fields are required';
							return;
						}

						$http.post('/users/ajax/password/changePasswordByUser', $.param({
							password: $scope.newPassword,
							confirm_password: $scope.newPasswordConfirm
						})).then(function (res) {
							if (!res.data.success) {
								$scope.currentError = res.data.error;
							} else {
								$uibModalInstance.close();
							}
						});
					}
				}
			};
			$rootScope.dialog(View.get('/users/change_password.html'), $uibModalScope, modalOptions);
		}

	}]);
