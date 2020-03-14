// Item Log
(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('CaseItemLogCtrl', [
		function () {

			var vm = this;

			var caseItem = null;

			vm.init = function (caseObj) {
				caseItem = caseObj;
			};

		}]);

})(opakeApp, angular);
