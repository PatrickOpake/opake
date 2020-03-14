(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('SettingsPrefCardStagesListCtrl', [
		'$controller',
		function ($controller) {

			var vm = this;

			$controller('SettingsAbstractEditableListCtrl', {vm: vm, options: {baseUrl: '/settings/databases/pref-card-stages/ajax'}});
		}]);

})(opakeApp, angular);
