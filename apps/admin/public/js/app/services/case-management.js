// Case Management
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('CaseManagement', ['$http', '$rootScope', 'CMConst', 'Case', function ($http, $rootScope, CMConst, Case) {

			var self = this;
			self.case = new Case();

			this.setCase = function (c) {
				self.case = c;

				if (!c.state) {
					var state = {};
					angular.forEach(CMConst.PHASES['clinical'], function (title, key) {
						state[key] = false;
					});
					self.case.state = state;
				}
			};

			this.getState = function () {
				return self.case.state;
			};

			this.saveState = function (checkStage) {
				var data = JSON.stringify(self.case.state);
				$http.post('/cases/ajax/' + $rootScope.org_id + '/updateState/' + self.case.id, $.param({data: data})).then(function (resp) {
					if (checkStage) {
						$rootScope.topMenuActive = resp.data.stage;
					}
				});
			};

			this.complete = function (key) {
				self.case.state[key] = true;
				self.saveState(true);
			};

			this.uncomplete = function (key) {
				self.case.state[key] = false;
				self.saveState(false);
			};

			this.isCompleted = function (key) {
				return self.case.state[key];
			};

		}]);
})(opakeApp, angular);
