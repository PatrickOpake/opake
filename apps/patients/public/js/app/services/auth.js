// Работа c текущим пользователем/организацией
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('AuthService', [
		'$http',
		'$q',
		'$rootScope',
		'$window',
		function ($http, $q, $rootScope, $window) {
			var currentUserPromise;

			this.login = function (data) {
				return $http.post('/api/auth/login', $.param({data: JSON.stringify(data)}));
			};

			this.logout = function () {
				$http.get('/api/auth/logout').then(function () {
					$window.location.href = '/' + ($rootScope.portal.alias || '');
				});
			};

			this.changePassword = function (data) {
				return $http.post('/api/auth/changePassword', $.param({data: JSON.stringify(data)}));
			};

			this.getUser = function () {
				if (!currentUserPromise) {
					loadAuthData();
				}
				return currentUserPromise;
			};

			this.reset = function () {
				currentUserPromise = null;
			};

			function loadAuthData() {
				var userDeferred = $q.defer();
				$http.get('/api/auth/user').then(function (result) {
					userDeferred.resolve(result.data);
				}, function (error) {
					userDeferred.reject(error);

				});
				currentUserPromise = userDeferred.promise;
			}

		}]);
})(opakeApp, angular);
