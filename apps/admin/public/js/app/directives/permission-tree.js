// Permission Tree
(function (opakeApp, angular) {
	'use strict';

	opakeApp.directive('permissionTree', [
		'$compile',

		function ($compile) {
		return {
			restrict: "E",
			terminal: true,
			scope: {
				settings: '=',
				hierarchy: '=',
				childLevel: '=',
				disabledPermissions: '=?'
			},
			link: function (scope, elem, attrs, ctrl) {

				if (!scope.childLevel) {
					scope.disabledPermissions = {};
					var hierarchyDepends = {};
					initDepends(scope.hierarchy);

					scope.$watchCollection('settings', function(settings) {
						scope.disabledPermissions = {};

						angular.forEach(settings, function(v, k) {
							if (!v && hierarchyDepends[k]) {
								angular.forEach(hierarchyDepends[k], function(name) {
									settings[name] = false;
									scope.disabledPermissions[name] = true;
								})
							}
						});
					});
				}

				var recursiveTemplate = '<permission-tree hierarchy="orgPermission.items" settings="settings" ng-if="orgPermission.items && orgPermission.items.length" child-level="true" disabled-permissions="disabledPermissions"></permission-tree>';
				var template = '<div ng-class="{\'child-level\': childLevel}" ng-repeat="orgPermission in hierarchy">' +
					'<div class="data-row">' +
					'<div class="status-switch">' +
					'<switch ng-model="settings[orgPermission.name]" show-labels="true" ng-disabled="disabledPermissions[orgPermission.name]"></switch> <span class="permission-label">{{orgPermission.label}}</span> <span class="permission-hint">{{orgPermission.hint}}</span>' +
					'</div>' +
					'</div>' +
					recursiveTemplate +
					'</div>';

				var newElement = angular.element(template);
				$compile(newElement)(scope);
				elem.replaceWith(newElement);

				function initDepends(hierarchy) {
					angular.forEach(hierarchy, function(v) {
						var name = v.name;
						if (v.depends) {
							angular.forEach(v.depends, function(dep) {
								if (!hierarchyDepends[dep]) {
									hierarchyDepends[dep] = [];
								}

								hierarchyDepends[dep].push(name);
							})
						}
						if (v.items) {
							initDepends(v.items);
						}
					})
				}

			}

		};
	}]);

})(opakeApp, angular);
