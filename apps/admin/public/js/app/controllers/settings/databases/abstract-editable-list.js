(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('SettingsAbstractEditableListCtrl', [
		'$rootScope',
		'$http',
		'$filter',
		'vm',
		'options',
		function ($rootScope, $http, $filter, vm, options) {

			var baseUrl = options.baseUrl;
			vm.toEdit = [];

			vm.search = function () {
				$http.get(baseUrl).then(function (response) {
					vm.items = response.data.items;
				});
			};
			vm.search();

			vm.edit = function (item) {
				var idx = vm.toEdit.indexOf(item);
				if (idx === -1) {
					vm.toEdit.push(item);
				}
			};

			vm.cancelEdit = function (item) {
				if (item.id) {
					var idx = vm.toEdit.indexOf(item);
					if (idx > -1) {
						vm.toEdit.splice(idx, 1);
					}
				} else {
					var idx = vm.items.indexOf(item);
					if (idx > -1) {
						vm.items.splice(idx, 1);
					}
				}
			};

			vm.isEditable = function (item) {
				return !item.id || vm.toEdit.indexOf(item) > -1;
			};

			vm.addNewItem = function () {
				var newItem = {};
				vm.items.push(newItem);
			};

			vm.showAddNewButton = function () {
				if (vm.items) {
					return $filter('filter')(vm.items, {id: '!'}).length < 10;
				}
				return false;
			};

			vm.save = function (item) {
				vm.errors = null;
				$http.post(baseUrl + '/save', $.param({data: JSON.stringify(item)})).then(function (result) {
					if (result.data.success) {
						item.id = result.data.id;
						vm.cancelEdit(item);
					} else {
						vm.errors = result.errors;
					}
				});
			};

			vm.delete = function (item) {
				$rootScope.dialog('/common/js/core/views/delete-confirm-modal.html').result.then(function () {
					$http.get(baseUrl + '/delete/' + item.id).then(function () {
						var idx = vm.items.indexOf(item);
						if (idx > -1) {
							vm.items.splice(idx, 1);
						}
					});
				});
			};

		}]);

})(opakeApp, angular);
