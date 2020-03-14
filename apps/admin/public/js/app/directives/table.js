// Smart edirable table
(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('opkTable', [function () {
			return {
				restrict: "A",
				transclude: true,
				scope: true,
				bindToController: {
					items: "=opkTable"
				},
				controller: function ($scope) {
					var tbl = this;

					tbl.current = null;
					tbl.tmpl = '';

					tbl.remove = function (index) {
						if (confirm('Are you sure?')) {
							tbl.items.splice(index, 1);
						}
					};

					tbl.add = function (item) {
						tbl.items.push(item);
					};

					tbl.addDialog = function (item) {
						tbl.current = item;
						tbl.current_exist = false;

						$scope.dialog(tbl.tmpl, $scope).result.then(function () {
							tbl.items.push(tbl.current);
							tbl.current = null;
						}, function () {
							tbl.current = null;
						});
					};

					tbl.editDialog = function (item) {
						tbl.current = angular.copy(item);
						tbl.current_exist = true;

						$scope.dialog(tbl.tmpl, $scope).result.then(function () {
							tbl.items[tbl.items.indexOf(item)] = tbl.current;
							tbl.current = null;
						}, function () {
							tbl.current = null;
						});
					};

				},
				controllerAs: 'tbl',
				link: function (scope, elem, attrs, ctrl, transclude) {

					ctrl.tmpl = attrs.tmpl;
					elem.addClass('opk-table');

					transclude(scope, function (clone) {
						elem.append(clone);
					});
				}
			};
		}]);

})(opakeApp, angular);
