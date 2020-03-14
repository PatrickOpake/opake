(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('SettingsUomListCtrl', [
		'$controller',
		function ($controller) {

			var vm = this;

			$controller('SettingsAbstractEditableListCtrl', {vm: vm, options: {baseUrl: '/settings/databases/uom/ajax'}});

			vm.searchFilter = function (item) {
				if (vm.name_search) {
					return !item.id || item.name.toLowerCase().indexOf(vm.name_search.toLowerCase()) !== -1;
				}
				return true;
			};
		}]);

})(opakeApp, angular);
