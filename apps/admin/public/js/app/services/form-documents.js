// App config
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('FormDocuments', [
		'$http',
		'$rootScope',
		'$window',
		'Tools',

		function ($http, $rootScope, $window,  Tools) {

			this.save = function (data, callback) {
				var fd = new FormData();

				angular.forEach(data, function(value, key) {
					fd.append(key, value);
				});

				return $http.post('/settings/forms/charts/ajax/' + $rootScope.org_id + '/upload/', fd, {
					withCredentials: true,
					headers: {'Content-Type': undefined},
					transformRequest: angular.identity
				}).then(function (result) {
					callback(result);
				});
			};

			this.delete = function (id, callback) {
				$http.post('/settings/forms/charts/ajax/' + $rootScope.org_id + '/delete/' + id).then(function (result) {
					if (callback) {
						callback(result);
					}
				});
			};

			this.update = function (data, action, callback) {
				return $http.post('/settings/forms/charts/ajax/' + $rootScope.org_id + '/update/', $.param({
					data: JSON.stringify(data),
					action: action
				})).then(function (result) {
					if(callback) {
						callback(result);
					}
				});
			};

			this.getForms = function (data, callback) {
				return $http.get('/settings/forms/charts/ajax/' + $rootScope.org_id, {params: data }).then(function (result) {
					if (callback) {
						callback(result);
					}
				});
			};

			this.downloadTemplate = function(doc, case_id) {
				var url = '/settings/forms/charts/ajax/' + $rootScope.org_id + '/exportDocument/' + doc.id;
				Tools.export(url, {caseid: case_id});
			};

		}]);
})(opakeApp, angular);
