// View state
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('ViewState', [
		'appInitData',
		'$rootScope',
		'$http',
		function (appInitData, $rootScope, $http) {
			var viewState = {};

			if (appInitData.viewState) {
				viewState = appInitData.viewState;
			}

			this.getState = function (key) {
				if (angular.isDefined(key)) {
					return viewState[key];
				}
				return viewState;
			};

			this.update = function (key, data) {
				return $http.post('/users/ajax/' + $rootScope.org_id + '/updateViewState/', $.param({
					key: key,
					data: JSON.stringify(data)
				})).then(function () {
					viewState[key] = data;
				});
			};

			// Custom methods
			this.getCasesViewDate = function () {
				if (viewState.cases_view_date) {
					return moment(viewState.cases_view_date).toDate();
				} else {
					return new Date();
				}
			};

			this.updateCasesViewDate = function (date) {
				this.update('cases_view_date', moment(date).format('YYYY-MM-DD'));
			};

		}]);
})(opakeApp, angular);
