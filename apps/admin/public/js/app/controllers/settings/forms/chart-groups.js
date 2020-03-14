(function (opakeApp, angular) {
	'use strict';

	opakeApp.controller('FormChatGroupsCtrl', [
		'$scope',
		'$http',
		'$q',
		'Tools',

		function ($scope, $http, $q, Tools) {

			var vm = this;

			vm.errors = null;
			vm.searchParams = {
				p: 0,
				l: 50
			};
			vm.items = [];
			vm.totalCount = null;
			vm.documents = [];
			vm.isResultsLoaded = false;
			vm.newGroup = null;
			vm.existedGroup = null;

			vm.isGroupPrinting = false;

			vm.init = function() {
				$http.get('/settings/forms/chart-groups/ajax/' + $scope.org_id + '/documents').then(function (result) {
					if (result.data) {
						vm.documents = result.data;
					}
				});
				vm.search();
			};

			vm.search = function () {
				vm.errors = null;
				var def = $q.defer();
				$http.get('/settings/forms/chart-groups/ajax/' + $scope.org_id, {params: vm.searchParams}).then(function (result) {
					if (result.data.items) {
						vm.items = result.data.items;
						vm.totalCount = result.data.total_count;
					}
					vm.isResultsLoaded = true;
					def.resolve();
				}, function() {
					def.reject();
				});

				return def.promise;
			};

			vm.getDocumentNamesList = function(group) {
				var names = [];
				if (group.documents) {
					angular.forEach(group.documents, function(doc) {
						names.push(doc.name);
					});
				}

				return names.join(', ');
			};

			vm.createGroup = function() {
				vm.existedGroup = null;
				vm.newGroup = {};
			};

			vm.editGroup = function(group) {
				vm.newGroup = null;
				vm.existedGroup = group;
			};

			vm.save = function(group) {
				$http.post('/settings/forms/chart-groups/ajax/' + $scope.org_id + '/save', $.param({data: JSON.stringify(group)})).then(function (result) {
					if (result.data.success) {
						vm.search().then(function() {
							vm.newGroup = null;
							vm.existedGroup = null;
						});
					} else {
						vm.errors = result.data.errors;
					}
				});
			};

			vm.cancel = function() {
				vm.newGroup = null;
				vm.existedGroup = null;
			};

			vm.deleteGroup = function(group) {
				$scope.dialog('settings/forms/chart-groups/confirm_delete.html', $scope).result.then(function () {
					$http.post('/settings/forms/chart-groups/ajax/' + $scope.org_id + '/delete', $.param({id: group.id})).then(function (result) {
						if (result.data.success) {
							vm.search();
						}
					});
				});

			};

			vm.printGroup = function(group) {
				vm.isGroupPrinting = true;
				$http.post('/settings/forms/chart-groups/ajax/' + $scope.org_id + '/compileChartGroup', $.param({id: group.id})).then(function (res) {
					vm.isGroupPrinting = false;
					if (res.data.success) {
						if (res.data.print) {
							Tools.print(location.protocol + '//' + location.host + res.data.url);
						} else {
							location.href = res.data.url;
						}

					}
				}, function() {
					vm.isGroupPrinting = false;
				});
			};

			vm.isGroupCreate = function() {
				return (!!vm.newGroup);
			};

			vm.isGroupEdit = function(group) {
				return (vm.existedGroup && group.id == vm.existedGroup.id);
			}

		}]);

})(opakeApp, angular);
