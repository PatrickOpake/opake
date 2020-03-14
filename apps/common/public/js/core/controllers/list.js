// Abstract List Controller
(function (opakeCore, angular) {
	'use strict';

	opakeCore.controller('ListCrtl', [
		'config',
		'vm',
		'options',
		function (config, vm, options) {

			var configSearchParams = {
				p: 0,
				l: config.pagination.limit
			};

			vm.toSelected = [];
			vm.selectAll = false;

			var defaultParams = angular.extend(configSearchParams, options.defaultParams || {});

			vm.search_params = angular.copy(defaultParams);

			vm.reset = function () {
				for (var key in vm.search_params) {
					delete vm.search_params[key];
				}
				for (var key in defaultParams) {
					vm.search_params[key] = defaultParams[key];
				}

				vm.search();
			};

			vm.addToSelected = function (item) {
				var idx = vm.toSelected.indexOf(item);
				if (idx > -1) {
					vm.toSelected.splice(idx, 1);
					if(!vm.toSelected.length) {
						vm.selectAll = false;
					}
				} else {
					vm.toSelected.push(item);
				}
				vm.selectAll = (vm.items && (vm.items.length == vm.toSelected.length));
			};

			vm.isAddedToSelected = function(item) {
				return vm.toSelected.indexOf(item) > -1;
			};

			vm.addToSelectedAll = function () {
				vm.toSelected = [];
				if (!vm.selectAll) {
					angular.forEach(vm.items, function (item) {
						vm.toSelected.push(item);
					});
				}
				vm.selectAll = !vm.selectAll;
			};

		}]);

})(opakeCore, angular);
