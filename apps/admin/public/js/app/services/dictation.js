// Dictation
(function (opakeApp, angular) {
	'use strict';

	opakeApp.service('Dictation', [
		'$timeout',
		function ($timeout) {
			var reloadInProgress = false;

			this.reload = function () {
				if (reloadInProgress) {
					return;
				}
				if (typeof NUSA_reinitializeVuiForm === 'function') {
					reloadInProgress = true;
					$timeout(function () {
						NUSA_reinitializeVuiForm();
						reloadInProgress = false;
					});
				}
			};

		}]);
})(opakeApp, angular);
