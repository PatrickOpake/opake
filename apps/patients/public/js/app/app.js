var opakeApp = angular.module('opake', [
	'opakeCore',
	'ui.router'
]);

/*opakeApp.config(['$compileProvider', function ($compileProvider) {
	$compileProvider.debugInfoEnabled(false);
}]);*/

opakeApp.run([
	'$rootScope',
	'$state',
	'$uibModal',
	'View',
	'Source',
	'appInitData',
	'Portal',
	'InactivityReminder',

	function ($rootScope, $state, $uibModal, View, Source, appInitData, Portal, InactivityReminder) {

		View.setMarker(new Date().getTime());

		$rootScope.$state = $state;
		$rootScope.source = Source;

		$rootScope.portal = new Portal(appInitData.portal);

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

	}]);
